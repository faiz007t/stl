<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

function saveLog($str) {
    $str = "[" . date("H:i:s") . "] " . $str . "\n";
    file_put_contents("logs-2.txt", $str, FILE_APPEND);
    echo $str;
}

function tunnel() {
    saveLog("Attempting connection to server...");
    exec("nohup python3 /root/akun/tunnel.py > /dev/null 2>&1 &");
    sleep(1);
    exec("nohup python3 /root/akun/ssh.py 1 > /dev/null 2>&1 &");
    saveLog("Connecting to the internet");
    for ($i = 1; $i <= 3; $i++) {
        saveLog("Checking connection, attempt: $i");
        sleep(3);
        exec("cat logs.txt 2>/dev/null | grep \"CONNECTED SUCCESSFULLY\"|awk '{print $4}'|tail -n1", $var);
        if (implode($var) == "SUCCESSFULLY") {
            exec("screen -dmS GProxy bash -c 'gproxy; exec sh'");
            saveLog("SUCCESSFULLY CONNECTED");
            break;
        } else {
            saveLog("Reconnect attempt $i in 3 seconds");
            exec("nohup python3 /root/akun/ssh.py 1 > /dev/null 2>&1 &");
        }
        saveLog("Failed!");
    }
}

function start() {
    if (file_exists("logs-2.txt")) unlink("logs-2.txt");
    saveLog("Starting STL");

    exec("cat /root/akun/stl.txt | awk 'NR==2'", $cek);
    if (empty(implode($cek))) {
        saveLog("You have not created a profile");
    } else {
        stop();  // stop existing connections before starting new
        exec("cat /root/akun/pillstl.txt", $pillstl);
        if (implode($pillstl) == "1") {
            exec("route -n | grep -i 0.0.0.0 | head -n1 | awk '{print $2}'", $ipmodem);
            exec('echo "ipmodem='.implode($ipmodem).'" > /root/akun/ipmodem.txt');
            exec("cat /root/akun/stl.txt | awk 'NR==2'", $host);
            exec("cat /root/akun/ipmodem.txt | grep -i ipmodem | cut -d= -f2 | tail -n1", $route);
            exec("ip tuntap add dev tun1 mode tun");
            exec("ifconfig tun1 10.0.0.1 netmask 255.255.255.0");
            tunnel();
            exec("route add 8.8.8.8 gw ".implode($route)." metric 0");
            exec("route add 8.8.4.4 gw ".implode($route)." metric 0");
            exec("route add ".implode($host)." gw ".implode($route)." metric 0");
            exec("route add default gw 10.0.0.2 metric 0");
        } else if (implode($pillstl) == "2") {
            tunnel();
        }
        exec("rm -r logs.txt 2>/dev/null");
        file_put_contents("/usr/bin/ping-stl", "#!/bin/bash\n#stl (Wegare)\nhttping m.google.com\n");
        exec("chmod +x /usr/bin/ping-stl");
        exec("/usr/bin/ping-stl > /dev/null 2>&1 &");
    }
}

function stop() {
    // Kill all background processes immediately to prevent further logging
    exec("screen -S GProxy -X quit");
    exec("killall -q badvpn-tun2socks ssh ping-stl sshpass httping python3 redsocks fping screen");

    exec("cat /root/akun/pillstl.txt", $pillstl);
    if (implode($pillstl) == "1") {
        exec("cat /root/akun/stl.txt | awk 'NR==2'", $host);
        exec("cat /root/akun/ipmodem.txt | grep -i ipmodem | cut -d= -f2 | tail -n1", $route);
        exec('route del 8.8.8.8 gw "'.implode($route).'" metric 0 2>/dev/null');
        exec('route del 8.8.4.4 gw "'.implode($route).'" metric 0 2>/dev/null');
        exec('route del "'.implode($host).'" gw "'.implode($route).'" metric 0 2>/dev/null');
        exec("ip link delete tun1 2>/dev/null");
    } else if (implode($pillstl) == "2") {
        exec("iptables -t nat -F OUTPUT 2>/dev/null");
        exec("iptables -t nat -F PROXY 2>/dev/null");
        exec("iptables -t nat -F PREROUTING 2>/dev/null");
    }

    exec("/etc/init.d/dnsmasq restart 2>/dev/null");
}

function autoReconnect() {
    $option = $_POST["option"];
    if ($option == "on") {
        file_put_contents("/etc/crontabs/root", "# BEGIN AUTOREKONEKSTL\n*/1 * * * *  autorekonek-stl\n# END AUTOREKONEKSTL\n", FILE_APPEND);
        exec("sed -i '/^$/d' /etc/crontabs/root 2>/dev/null");
        exec("/etc/init.d/cron restart");
        echo "Enable Success";
    } else {
        exec('sed -i "/^# BEGIN AUTOREKONEKSTL/,/^# END AUTOREKONEKSTL/d" /etc/crontabs/root > /dev/null');
        exec("/etc/init.d/cron restart");
        echo "Disable Success";
    }
}

function saveConfig() {
    $title = trim($_POST["title"]);
    $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '', $title);
    $dir = "/root/akun/configs/";

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = $dir . $safeTitle . ".json";

    $metString = $_POST["met"] ?? '';
    $met = explode("|", $metString);
    $method = $met[0] ?? '';
    $connection_mode = $met[1] ?? '';

    $configData = [
        "title" => $title,
        "method" => $method,
        "connection_mode" => $connection_mode,
        "pillstl" => $_POST["pillstl"] ?? '',
        "host" => $_POST["host"] ?? '',
        "port" => $_POST["port"] ?? '',
        "udp" => $_POST["udp"] ?? '',
        "user" => $_POST["user"] ?? '',
        "pass" => $_POST["pass"] ?? '',
        "proxy" => $_POST["proxy"] ?? '',
        "pp" => $_POST["pp"] ?? '',
        "bug" => $_POST["bug"] ?? '',
        "payload" => $_POST["payload"] ?? ''
    ];

    file_put_contents($filename, json_encode($configData, JSON_PRETTY_PRINT));

    echo "Profile saved successfully as " . basename($filename);
}

$action = $_POST["action"] ?? '';

switch ($action) {
    case "start":
        start();
        break;
    case "stop":
        // Clear log file before stopping to avoid unwanted log entries
        if (file_exists("logs-2.txt")) unlink("logs-2.txt");
        stop();
        saveLog("SUCCESSFULLY STOPPED"); // Only show this one final log on stop
        break;
    case "saveConfig":
        saveConfig();
        break;
    case "autoBootRecon":
        autoReconnect();
        break;
    case "loadConfig":
        $title = $_POST["title"] ?? '';
        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '', $title);
        $file = "/root/akun/configs/" . $safeTitle . ".json";
        if (file_exists($file)) {
            header('Content-Type: application/json');
            echo file_get_contents($file);
        } else {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Config not found"]);
        }
        break;
    case "clearLog":
        if (file_exists("logs-2.txt")) {
            file_put_contents("logs-2.txt", ""); // clear the log file content
            echo "Log cleared";
        } else {
            echo "Log file not found";
        }
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(["error" => "Invalid action"]);
}
?>
