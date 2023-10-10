#!/usr/bin/env php
<?php

declare(strict_types=1);

function d(...$args)
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    $file = $trace['file'];
    $line = $trace['line'];
    echo "d() called at {$file}:{$line}" . PHP_EOL;
    var_dump(...$args);
}
function git_diff(string $str1, string $str2): string
{
    $tmp1h = tmpfile();
    $tmp2h = tmpfile();
    $tmp1f = stream_get_meta_data($tmp1h)['uri'];
    $tmp2f = stream_get_meta_data($tmp2h)['uri'];
    fwrite($tmp1h, $str1);
    fwrite($tmp2h, $str2);
    // --word-diff-regex=. --word-diff=color
    $cmd = "git diff --no-index --color " . escapeshellarg($tmp1f) . " " . escapeshellarg($tmp2f);
    $ret = shell_exec($cmd);
    fclose($tmp1h);
    fclose($tmp2h);
    // remove first 4 lines: (todo: optimize)
    $ret = implode("\n", array_slice(explode("\n", $ret), 4));
    $ret = strtr($ret, array(
        '\\ No newline at end of file' => '',
    ));
    return $ret;
}

class DNS
{
    public static function generate_dig_command(string $domain = "example.com", string $type = "A", string $dns_server = "8.8.8.8"): string
    {
        $cmd = array(
            'dig',
            '@' . escapeshellarg($dns_server),
            escapeshellarg($domain),
            $type,
            "+cl +ttlid +nomultiline"
            //'+all',
        );
        $cmd = implode(' ', $cmd);
        return $cmd;
    }
    public static function get_records_raw(string $domain = "example.com", string $type = "A", string $dns_server = "8.8.8.8"): string
    {
        global $args;
        $cache_enabled = $args['cache'];
        if ($cache_enabled) {
            static $cache = null;
            static $fetcher = null;
            if ($cache === null) {
                $cache = new \PDO('sqlite:' . __FILE__ . '.dns_cache.sqlite3', null, null, array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ));
                $cache->exec('CREATE TABLE IF NOT EXISTS dns_cache (domain TEXT NOT NULL, type TEXT NOT NULL, dns_server TEXT NOT NULL, records BLOB NOT NULL, PRIMARY KEY(domain, type, dns_server))');
                $fetcher = $cache->prepare('SELECT records FROM dns_cache WHERE domain = ? AND type = ? AND dns_server = ?');
            }
            $fetcher->execute(array(
                $domain,
                $type,
                $dns_server,
            ));
            $cached = $fetcher->fetch();
            if ($cached !== false) {
                return $cached['records'];
            }
        }
        $cmd = self::generate_dig_command($domain, $type, $dns_server);
        $raw = shell_exec($cmd);
        //d(["cmd" => $cmd, "raw" => $raw]);
        if ($cache_enabled) {
            $cache->prepare('INSERT INTO dns_cache (domain, type, dns_server, records) VALUES (?, ?, ?, ?)')->execute(array(
                $domain,
                $type,
                $dns_server,
                $raw,
            ));
        }
        return $raw;
    }
    public static function get_records_parsed(string $domain = "example.com", string $type = "A", string $dns_server = "8.8.8.8"): array
    {
        $very_raw = self::get_records_raw($domain, $type, $dns_server);
        $raw = $very_raw;
        $answer_section_pos_needle = ";; ANSWER SECTION:";
        $answer_section_pos = strpos($raw, $answer_section_pos_needle);
        if ($answer_section_pos === false) {
            if (str_contains($raw, "ANSWER: 0")) {
                return array();
            }
            throw new \LogicException("ANSWER SECTION not found in raw output: " . var_export($very_raw, true));
        }
        $raw = substr($raw, $answer_section_pos);
        $next_section_pos = strpos($raw, "SECTION:\n", strlen($answer_section_pos_needle));
        if ($next_section_pos !== false) {
            $raw = substr($raw, 0, $next_section_pos);
        }
        $ret = array();
        $lines = explode("\n", $raw);
        foreach ($lines as $line) {
            //$line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_starts_with($line, ';')) {
                continue;
            }
            //d($line);
            $data = $line;
            // with the exception of [4], the number of spaces/tabs depends on domain length.
            // without this preg_split, strange things happen to domains like 
            // thelongestdomainnameintheworldandthensomeandthensomemoreandmore.com
            $data = preg_split('/\s+/', $data, 5);
            $data = array_values($data);
            $declared_type = $data[3];
            if (!array_key_exists($declared_type, self::DNS_RECORD_TYPES)) {
                throw new \LogicException("Unknown type: " . var_export($declared_type, true) . ". data: " . var_export($data, true) . " raw: " . var_export($very_raw, true));
            }
            if ($declared_type === "MX") {
                // MX has a quirk where priority is not in a tab-group but separated from  HOST by a space
                if (count($data) !== 5) {
                    throw new \LogicException("Expected 5 columns but got " . count($data) . ": " . var_export($data, true));
                }
                $tmp = explode(" ", $data[4]);
                if (count($tmp) !== 2) {
                    throw new \LogicException("Expected data[4] to contain priorty(space)domain but could not find space: " . var_export($data, true) . " raw: " . var_export($very_raw, true));
                }
                $data[4] = $tmp[0]; // PRIORITY
                $data[5] = $tmp[1]; // HOST
                unset($tmp);
            }
            if ($declared_type === "SOA") {
                // SOA has 7 things separated by space in [4] ...
                if (count($data) !== 5) {
                    throw new \LogicException("Expected 5 in data columns but got " . count($data) . ": " . var_export($data, true));
                }
                $tmp = explode(" ", $data[4]);
                if (count($tmp) !== 7) {
                    throw new \LogicException("Expected 7 columns in data[4] but got " . count($tmp) . ": " . var_export($tmp, true));
                }
                $data[4] = $tmp[0]; // MNAME
                $data[5] = $tmp[1]; // RNAME
                $data[6] = $tmp[2]; // SERIAL
                $data[7] = $tmp[3]; // REFRESH
                $data[8] = $tmp[4]; // RETRY
                $data[9] = $tmp[5]; // EXPIRE
                $data[10] = $tmp[6]; // MINIMUM
                unset($tmp);
            }
            if (count($data) !== count(self::DNS_RECORD_TYPES[$declared_type])) {
                throw new \LogicException("Unexpected number of columns, expected " . count(self::DNS_RECORD_TYPES[$declared_type]) . " but got " . count($data) . ": " . var_export($data, true) . "\nraw: " . var_export($very_raw, true));
            }
            $data = array_combine(self::DNS_RECORD_TYPES[$declared_type], $data);
            $int_list = array(
                'TTL',
                'PRIORITY',
                'SERIAL',
                'REFRESH',
                'RETRY',
                'EXPIRE',
                'MINIMUM',
            );
            foreach ($int_list as $int_key) {
                if (!array_key_exists($int_key, $data)) {
                    continue;
                }
                $int = filter_var($data[$int_key], FILTER_VALIDATE_INT);
                if ($int === false) {
                    throw new \LogicException("{$int_key} is not an integer: " . var_export($data[$int_key], true));
                }
                $data[$int_key] = $int;
            }
            $ret[] = $data;
        }
        return $ret;
    }
    public const DNS_RECORD_TYPES = array(
        "A" => array(
            // IPv4 address
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "IP",
        ),
        "AAAA" => array(
            // IPv6 address
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "IP",
        ),
        "AFSDB" => array(
            // TODO
            // Andrew File System database server record
        ),
        "APL" => array(
            // TODO
            // Address Prefix List
        ),
        "CAA" => array(
            // todo, is this really correct?
            // this website says our CAA record layout is wrong: https://sslmate.com/caa/
            'DOMAIN',
            'TTL',
            'CLASS',
            'TYPE',
            'DOMAIN'
        ),
        "CDNSKEY" => array(
            // TODO
            // Child DNSKEY (something to do with DNSSEC)
        ),
        "CDS" => array(
            // TODO
        ),
        "CERT" => array(
            // TODO
        ),
        "CNAME" => array(
            'DOMAIN',
            'TTL',
            'CLASS',
            'TYPE',
            'DOMAIN'
        ),
        "CSYNC" => array(
            // TODO
        ),
        "DHCID" => array(
            // TODO
        ),
        "DLV" => array(
            // TODO
        ),
        "DNAME" => array(
            // TODO
        ),
        "DNSKEY" => array(
            // TODO
        ),
        "DS" => array(
            // TODO
        ),
        "EUI48" => array(
            // TODO
        ),
        "EUI64" => array(
            // TODO confirm this..
            "DOMAIN",
            "TTL",
            "CLASS",
            "TYPE",
            "DOMAIN",
        ),
        "HINFO" => array(
            "DOMAIN",
            "TTL",
            "CLASS",
            "TYPE",
            // todo: figure out what [4] is
            "unknown1",
        ),
        "HIP" => array(
            // TODO
        ),
        "IPSECKEY" => array(
            // TODO
        ),
        "KEY" => array(
            // TODO
        ),
        "KX" => array(
            // TODO
        ),
        "LOC" => array(
            // TODO
        ),
        "MX" => array(
            // MX records specify the mail server responsible for accepting email messages on behalf of a domain name
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "PRIORITY",
            5 => "HOST",
        ),
        "NAPTR" => array(
            // TODO
        ),
        'NS' => array(
            // NS records are used by top level domain servers to direct traffic to the Content DNS server which contains the authoritative DNS records
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "HOST",
        ),
        "NSEC" => array(
            // TODO
        ),
        "NSEC3" => array(
            // TODO
        ),
        "NSEC3PARAM" => array(
            // TODO
        ),
        "OPENPGPKEY" => array(
            // TODO
        ),
        "PTR" => array(
            // TODO
        ),
        'RRSIG' => array(
            // RRSIG records are used to authenticate DNS records using digital signatures so as to protect against DNS cache poisoning
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            // todo: figure out what 4 is
            4 => "unknown1",
        ),
        "RP" => array(
            // TODO
        ),
        "SIG" => array(
            // TODO
        ),
        "SMIMEA" => array(
            // TODO
        ),
        'SOA' => array(
            // SOA records are used to determine how long a DNS resolver should cache DNS information
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "MNAME",
            5 => "RNAME",
            6 => "SERIAL",
            7 => "REFRESH",
            8 => "RETRY",
            9 => "EXPIRE",
            10 => "MINIMUM",
        ),
        "SRV" => array(
            // TODO
        ),
        "SSHFP" => array(
            // TODO
        ),
        "TA" => array(
            // TODO
        ),
        "TKEY" => array(
            // TODO
        ),
        "TLSA" => array(
            // TODO
        ),
        "TSIG" => array(
            // TODO
        ),
        'TXT' => array(
            // TXT records are used to store any text-based information that can be grabbed when necessary
            0 => "DOMAIN",
            1 => "TTL",
            2 => "CLASS",
            3 => "TYPE",
            4 => "TXT",
        ),
        "URI" => array(
            // TODO
        ),
        "ZONEMD" => array(
            // TODO
        ),
    );
    public const COMMON_SUBDOMAINS = array(
        'www',
        'mail',
        'webmail',
        'smtp',
        'pop',
        'pop3',
        'imap',
        'imap4',
        'ftp',
        'cpanel',
        'whm',
        'webdisk',
        'ns1',
        'ns2',
        'ns3',
        'ns4',
    );
}

$args = array(
    "dns1" => null,
    "dns2" => null,
    "domain" => null,
    "ignore_columns" => "",
    "cache" => false,
);
$dns1 = &$args['dns1'];
$dns2 = &$args['dns2'];
$domain = &$args['domain'];
$sample_usage = "php {$argv[0]} ignore_columns=TTL dns1=8.8.8.8 dns2=ns1.dreamhost.com domain=example.com\n";
foreach (array_slice($argv, 1) as $arg) {
    $explode = explode("=", $arg, 2);
    $key = $explode[0];
    $value = $explode[1] ?? null;
    if (!array_key_exists($key, $args)) {
        fwrite(STDERR, "Unknown argument: {$key}\n{$sample_usage}\n");
        exit(1);
    }
    if ($key === "cache") {
        $args[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        continue;
    }
    $args[$key] = $value;
}
foreach ($args as $key => $value) {
    if ($value === null) {
        fwrite(STDERR, "Missing argument: {$key}\n{$sample_usage}\n");
        exit(1);
    }
}

$domains_to_check = array(
    $domain
);
if (1 || substr_count($domain, ".") < 2) {
    foreach (DNS::COMMON_SUBDOMAINS as $subdomain) {
        $domains_to_check[] = $subdomain . "." . $domain;
    }
}
$o = new DNS();
foreach ($domains_to_check as $domain) {
    foreach (array_keys(DNS::DNS_RECORD_TYPES) as $type) {
        //echo ".";
        $str = "\r{$type} {$domain}" . str_repeat(" ", 10) . "\r";
        fwrite(STDERR, $str);
        $dns1records = $o->get_records_parsed($domain, $type, $dns1);
        $dns2records = $o->get_records_parsed($domain, $type, $dns2);
        $dns1records_ignored_columns = $dns1records;
        $dns2records_ignored_columns = $dns2records;
        if ($args['ignore_columns'] !== "") {
            $ignore_columns = explode(",", $args['ignore_columns']);
            foreach ($ignore_columns as $ignore_column) {
                foreach ($dns1records as $key => $record) {
                    unset($dns1records[$key][$ignore_column]);
                }
                foreach ($dns2records as $key => $record) {
                    unset($dns2records[$key][$ignore_column]);
                }
            }
        }
        if ($dns1records_ignored_columns !== $dns2records_ignored_columns) {
            echo "DNS records differ for {$domain} {$type} dns1={$dns1} dns2={$dns2}\n";
            echo $o::generate_dig_command($domain, $type, $dns1) . PHP_EOL;
            echo $o::generate_dig_command($domain, $type, $dns2) . PHP_EOL;
            if (false) {
                echo "DNS1:\n";
                var_dump($dns1records);
                echo "DNS2:\n";
                var_dump($dns2records);
            }
            echo "Diff:\n";
            echo git_diff(var_export($dns1records_ignored_columns, true), var_export($dns2records_ignored_columns, true));
        }
    }
}
