<?php
// Load Title and other config values from files for form prefill
exec("cat /root/akun/title.txt", $title2); // Load saved title

exec("cat /root/akun/stl.txt | awk 'NR==1'", $met2);
exec("cat /root/akun/stl.txt | awk 'NR==2'", $host2);
exec("cat /root/akun/stl.txt | awk 'NR==3'", $port2);
exec("cat /root/akun/stl.txt | awk 'NR==4'", $user2);
exec("cat /root/akun/stl.txt | awk 'NR==5'", $pass2);
exec("cat /root/akun/stl.txt | awk 'NR==6'", $udp2);
exec("cat /root/akun/stl.txt | awk 'NR==7'", $payload2);
exec("cat /root/akun/stl.txt | awk 'NR==8'", $proxy2);
exec("cat /root/akun/stl.txt | awk 'NR==9'", $pp2);
exec("cat /root/akun/stl.txt | awk 'NR==10'", $bug2);
exec("cat /root/akun/pillstl.txt", $pillstl2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<title>STL Tunnel - Config</title>
	<meta name="description" content="Portal Free VPN Sites Provider oleh Helmi Amirudin.">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/img/og-16.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/img/og-32.png">
	<link rel="icon" type="image/png" sizes="180x180" href="assets/img/og-180.png">
	<link rel="icon" type="image/png" sizes="192x192" href="assets/img/og-192.png">
	<link rel="icon" type="image/png" sizes="512x512" href="assets/img/og-512.png">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<style>
        body {
            background: 
                linear-gradient(rgba(18, 22, 32, 0.8), rgba(18, 22, 32, 0.8)),
                url('assets/img/background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #1e293b !important;
        }
        .nav-link,
        .nav-link:focus,
        .nav-link:hover,
        .nav-item.active > .nav-link {
            color: #ffffff !important;
            font-weight: 600;
        }
        .nav-item.active > .nav-link {
            border-bottom: 2px solid #4f46e5;
        }
        .card {
            background-color: #27314f !important;
            border-radius: 8px;
            border: none;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        h4 {
            color: #a5b4fc;
        }
        label {
            color: #cbd5e1;
            font-weight: 500;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.315);
            box-shadow: 0 0 10px 0 #0320FC;
            color: white;
            border-radius: 0.5rem;
            text-align: center;
        }
        .form-control:focus {
            border-color: #2CC300;
            color: #fff;
            box-shadow: 0 0 5px 0 #2CC300 inset, 0 0 5px 2px #2CC300;
            background-color: rgba(8, 8, 8, 0.64);
        }
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #3730a3;
            border-color: #3730a3;
        }
        .box-shadow {
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
	</style>
</head>
<body>
    <div class="container">
        <div class="row py-2">
            <div class="col-lg-6 col-md-12 mx-auto mt-3">
                <div class="card bg-transparent box-shadow">
                    <div class="col-lg-12">
                        <h4 class="text-center my-4">STL by Wegare</h4>
                    </div>
                    <nav class="navbar navbar-expand-sm">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
                                aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-md-center" id="navbar">
                            <strong>
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php">Home</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="config.php">Config</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="tentang.php">About</a>
                                    </li>
                                </ul>
                            </strong>
                        </div>
                    </nav>
                    <div class="card-body">
                        <div class="form-row pb-lg-2">
                            <div class="col-md-12">
                                <label>Title</label>
                                <input type="text" class="form-control" placeholder="Enter Title" id="title" value="<?php if (isset($title2[0])) echo htmlspecialchars($title2[0]); ?>" required>
                            </div>
                        </div>
                        <div class="form-row pb-lg-2">
                            <div class="col-md-6">
                                <label>Mode</label>
                                <select class="form-control" id="met" onchange="mode(this.value)" required>
                                    <option value="http" <?php if (isset($met2[0]) && $met2[0] == "http") echo "selected"; ?>>Http Proxy + Payload</option>
                                    <option value="https" <?php if (isset($met2[0]) && $met2[0] == "https") echo "selected"; ?>>SSL/TLS Direct</option>
                                    <option value="direct" <?php if (isset($met2[0]) && $met2[0] == "direct") echo "selected"; ?>>SSH Direct + Payload</option>
                                    <option value="sp" <?php if (isset($met2[0]) && $met2[0] == "sp") echo "selected"; ?>>SSL/TLS + Payload</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Socks Proxy</label>
                                <select class="form-control" id="pillstl" required>
                                    <option value="1" <?php if (isset($pillstl2[0]) && $pillstl2[0] == "1") echo "selected"; ?>>Badvpn-Tun2socks</option>
                                    <option value="2" <?php if (isset($pillstl2[0]) && $pillstl2[0] == "2") echo "selected"; ?>>Transparent Proxy</option>
                                </select>
                            </div>
                        </div>
                        <div class="pb-lg-2">
                            <div class="form-row pb-lg-2">
                                <div class="col-md-6">
                                    <label>Host/IP Address</label>
                                    <input type="text" class="form-control" placeholder="server.com" value="<?php if (isset($host2[0])) echo htmlspecialchars($host2[0]); ?>" id="host" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Port</label>
                                    <input type="number" class="form-control" placeholder="443" value="<?php if (isset($port2[0])) echo htmlspecialchars($port2[0]); ?>"  id="port" required>
                                </div>
                                <div class="col-md-3">
                                    <label>UDPGW Port</label>
                                    <input type="number" class="form-control" placeholder="7300" value="<?php if (isset($udp2[0])) echo htmlspecialchars($udp2[0]); ?>" id="udp" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label>Username</label>
                                    <input type="text" class="form-control" placeholder="Username" value="<?php if (isset($user2[0])) echo htmlspecialchars($user2[0]); ?>" id="user" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Password</label>
                                    <input type="text" class="form-control" placeholder="Password" value="<?php if (isset($pass2[0])) echo htmlspecialchars($pass2[0]); ?>" id="pass" required>
                                </div>
                                <div class="col-md-4" id="dBug">
                                    <label>SNI</label>
                                    <input type="text" class="form-control" placeholder="bug.com" value="<?php if (isset($bug2[0])) echo htmlspecialchars($bug2[0]); ?>"  id="bug">
                                </div>
                                <div class="col-md-4" id="dProxy">
                                    <label>IP Proxy</label>
                                    <input type="text" class="form-control" placeholder="127.0.0.1" value="<?php if (isset($proxy2[0])) echo htmlspecialchars($proxy2[0]); ?>" id="proxy">
                                </div>
                                <div class="col-md-4" id="dPP">
                                    <label>Port Proxy</label>
                                    <input type="text" class="form-control" placeholder="8080" value="<?php if (isset($pp2[0])) echo htmlspecialchars($pp2[0]); ?>" id="pp">
                                </div>
                            </div>
                            <div class="pb-lg-2">
                                <div class="form-group" id="dPayload">
                                    <label>Payload</label>
                                    <textarea style="text-align:left" class="form-control" rows="5" placeholder="GET [http://server.com/](http://server.com/) HTTP/1.1[crlf][crlf]CONNECT [host_port] HTTP/1.1[crlf]Connection: keep-allive[crlf][crlf]" id="payload" required><?php if (isset($payload2[0])) echo htmlspecialchars($payload2[0]); ?></textarea>
                                </div>
                            </div>
                            <div class="pb-lg-2 text-center">
                                <button type="submit" onclick="saveConfig();" id="saveConfig" class="btn btn-primary btn-block">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/main.js?<?= time(); ?>"></script>
<script>
function saveConfig() {
    const data = {
        action: 'saveConfig',
        title: document.getElementById('title').value,
        met: document.getElementById('met').value,
        pillstl: document.getElementById('pillstl').value,
        host: document.getElementById('host').value,
        port: document.getElementById('port').value,
        udp: document.getElementById('udp').value,
        user: document.getElementById('user').value,
        pass: document.getElementById('pass').value,
        proxy: document.getElementById('proxy').value,
        pp: document.getElementById('pp').value,
        bug: document.getElementById('bug').value,
        payload: document.getElementById('payload').value
    };
    $.post('api.php', data, function(response) {
        alert(response);
    });
}
</script>
</body>
</html>
