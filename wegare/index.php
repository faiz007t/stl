<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

// Get selected config from URL parameter to keep selection after reload
$selectedConfig = $_GET['config'] ?? '';

// Load and filter logs on page load to exclude unwanted lines
if (!file_exists("logs-2.txt")) {
    touch("logs-2.txt");
}
$rawLog = file_get_contents("logs-2.txt");
$logLines = explode("\n", $rawLog);
$filteredLines = array_filter($logLines, function($line) {
    return strpos($line, 'Menghentikan STL') === false && strpos($line, 'Stop Sukses') === false;
});
$log = implode("\n", $filteredLines);

// Check if Auto Boot Reconnect enabled by scanning crontab
$checked = false;
if (file_exists("/etc/crontabs/root")) {
    $crontabContent = file_get_contents("/etc/crontabs/root");
    if (strpos($crontabContent, "autorekonek-stl") !== false) {
        $checked = true;
    }
}

// Detect if config/service is running by checking PID file with safer validation
$running = false;
$pidFile = "/root/akun/stl_running.pid";  // Change this path as needed
if (file_exists($pidFile)) {
    $pid = trim(file_get_contents($pidFile));
    if (ctype_digit($pid) && (int)$pid > 0) {
        if (function_exists('posix_getpgid')) {
            if (posix_getpgid((int)$pid) !== false) {
                $running = true;
            }
        } else {
            if (is_dir("/proc/$pid")) {
                $running = true;
            }
        }
    }
}

// Load config filenames
$configDir = '/root/akun/configs/';
$configFiles = [];
if (is_dir($configDir)) {
    $files = scandir($configDir);
    foreach ($files as $file) {
        if (preg_match('/\.json$/', $file)) {
            $configFiles[] = basename($file, '.json');
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
<title>STL Tunnel - Home</title>
<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
<style>
body {
  background: linear-gradient(rgba(18,22,32,0.8), rgba(18,22,32,0.8)),
              url('assets/img/background.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #e1e1e1;
}
/* Center config selection */
.config-select-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 15px;
}
.config-select-container label {
  font-weight: 600;
  margin-bottom: 8px;
  color: #cbd5e1;
}
.config-select-container select {
  background-color: rgba(255, 255, 255, 0.315);
  box-shadow: 0 0 10px 0 #0320FC;
  color: white;
  border-radius: 0.5rem;
  border: none;
  padding: 0.375rem 0.75rem;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  font-weight: 600;
  text-align: center;
  max-width: 320px;
  width: 100%;
}
.config-select-container select:focus {
  border-color: #2CC300;
  color: #fff;
  box-shadow: 0 0 5px 0 #2CC300 inset, 0 0 5px 2px #2CC300;
  background-color: rgba(8, 8, 8, 0.64);
  outline: none;
}
.config-select-container select::-ms-expand {
  display: none;
}

.nav-link,
.nav-link:hover,
.nav-link:focus,
.nav-item.active > .nav-link {
  color: #fff !important;
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
.btn {
  margin-right: 0.5rem;
  font-weight: 600;
}
.btn-primary {
  background-color: #4f46e5;
  border-color: #4f46e5;
  transition: background-color 0.3s ease;
}
.btn-primary:hover, .btn-primary:focus {
  background-color: #3730a3;
  border-color: #3730a3;
}
.btn-danger {
  background-color: #ef4444;
  border-color: #ef4444;
}
.btn-danger:hover, .btn-danger:focus {
  background-color: #b91c1c;
  border-color: #b91c1c;
}
.btn-success {
  background-color: #22c55e;
  border-color: #22c55e;
}
.btn-success:hover, .btn-success:focus {
  background-color: #15803d;
  border-color: #15803d;
}
textarea#log {
  background-color: #1e293b;
  color: #e0e7ff;
  border: 1px solid #4f46e5;
  border-radius: 4px;
  font-family: monospace;
  width: 100%;
  height: 15rem;
  resize: none;
  white-space: pre-wrap;
}
label {
  font-weight: 600;
  color: #e1e1e1;
}
.container {
  margin-top: 30px;
  margin-bottom: 30px;
}
.form-check-label {
  color: #e1e1e1;
  font-weight: 600;
}
</style>
</head>
<body>
<div class="container">
  <div class="row py-2">
    <div class="col-lg-6 col-md-12 mx-auto mt-3">
      <div class="card bg-transparent box-shadow">
        <h4 class="text-center my-4">STL Modified by faisal971</h4>
        <nav class="navbar navbar-expand-sm">
          <button class="navbar-toggler" type="button" data-toggle="collapse"
                  data-target="#navbar" aria-controls="navbar" aria-expanded="false"
                  aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-md-center" id="navbar">
            <ul class="navbar-nav font-weight-bold">
              <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li>
              <li class="nav-item"><a class="nav-link" href="config.php">Config</a></li>
              <li class="nav-item"><a class="nav-link" href="tentang.php">About</a></li>
            </ul>
          </div>
        </nav>
        <div class="card-body">
          <!-- Centered config selector -->
          <div class="form-group config-select-container">
            <label for="configSelect">Choose Configuration</label>
            <select id="configSelect" class="form-control" onchange="loadConfig(this.value);">
              <option value="">-- Select Config --</option>
              <?php foreach ($configFiles as $conf): ?>
                <option value="<?= htmlspecialchars($conf) ?>" <?= ($conf === $selectedConfig) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($conf) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group form-check text-center mb-4">
            <input type="checkbox" class="form-check-input" id="autoBootRecon" <?= $checked ? 'checked' : '' ?> />
            <label class="form-check-label" for="autoBootRecon">Auto Booting & Auto Reconnect</label>
          </div>
          <div class="form-group text-center">
            <button type="button" id="start" class="btn btn-primary" onclick="start()">Start</button>
            <button type="button" id="stop" class="btn btn-danger" onclick="stop()" disabled>Stop</button>
            <button type="button" id="clearLog" class="btn btn-success">Clear</button>
          </div>
          <div class="form-group">
            <textarea id="log" disabled><?= htmlspecialchars($log) ?></textarea>
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
  let logInterval = null;
  let logsStarted = <?= json_encode($running) ?>;  // True if service running, else false

  function updateButtonStates() {
    if (logsStarted) {
      $('#start').prop('disabled', true);
      $('#stop').prop('disabled', false);
    } else {
      $('#start').prop('disabled', false);
      $('#stop').prop('disabled', true);
    }
  }

  $(document).ready(function() {
    updateButtonStates();
    if (logsStarted) {
      if (logInterval) clearInterval(logInterval);
      logInterval = setInterval(loadLogs, 3000);
      loadLogs();
    }
  });

  $('#clearLog').on('click', function() {
    // Stop auto-refresh interval to prevent overwriting cleared logs
    if (logInterval) {
      clearInterval(logInterval);
      logInterval = null;
    }
    logsStarted = false;
    updateButtonStates();

    // Clear the logs textarea
    $('#log').val('');
  });

  $('#autoBootRecon').on('change', function() {
    const enabled = $(this).is(':checked') ? 'on' : 'off';
    $.post('api.php', { action: 'autoBootRecon', option: enabled });
  });

  function loadConfig(configName) {
    if (!configName) return;
    $.post('api.php', { action: 'loadConfig', title: configName }, function(data) {
      if (data.error) return;
      const url = new URL(window.location.href);
      url.searchParams.set('config', configName);
      window.location.href = url.toString();
    }, 'json');
  }

  function loadLogs() {
    if (!logsStarted) {
      if (logInterval) {
        clearInterval(logInterval);
        logInterval = null;
      }
      $('#log').val('');
      return;
    }
    $.get('logs-2.txt', function(data) {
      const lines = data.split('\n');
      const filteredLines = lines.filter(line => {
        return !line.includes('Menghentikan STL') && !line.includes('Stop Sukses');
      });
      $('#log').val(filteredLines.join('\n'));
    });
  }

  function start() {
    logsStarted = true;
    updateButtonStates();
    $.post('api.php', { action: 'start' }, function() {
      if (logInterval) clearInterval(logInterval);
      logInterval = setInterval(loadLogs, 3000);
      loadLogs();
    });
  }

  function stop() {
    logsStarted = false;
    updateButtonStates();
    if (logInterval) {
      clearInterval(logInterval);
      logInterval = null;
    }
    $.post('api.php', { action: 'stop' }, function() {
      $('#log').val('Logs stopped.');
    });
  }
</script>
</body>
</html>
