<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syslog Message Generator</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f6f6f6;
        }
        .syslog-preview {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            background-color: #333;
            color: #0f0;
            padding: 1rem;
            border-radius: 0.25rem;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-top: 1rem;
        }
        .result-box {
            border-left: 4px solid var(--bs-primary);
            background-color: #fff;
        }
        .info-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title mb-4">
                    <i class="bi bi-send-fill me-2" style="font-size: 2rem;"></i>
                    Syslog Message Generator
                </h1>
                
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2 flex-shrink-0" style="font-size: 1.25rem;"></i>
                    <div>
                        <strong>Information:</strong> This script allows you to compose and send messages in Syslog format (RFC 3164) to a remote Syslog server.
                    </div>
                </div>

                <form method="POST" action="" id="syslogForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="syslog_server" class="form-label">Syslog Server <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="syslog_server" name="syslog_server" 
                                   value="<?php echo isset($_POST['syslog_server']) ? htmlspecialchars($_POST['syslog_server']) : '127.0.0.1'; ?>" 
                                   placeholder="192.168.1.100" required pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$|^[a-zA-Z0-9.-]+$">
                            <div class="form-text">IP address or hostname of the Syslog server</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="syslog_port" class="form-label">Syslog Port <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="syslog_port" name="syslog_port" 
                                   value="<?php echo isset($_POST['syslog_port']) ? htmlspecialchars($_POST['syslog_port']) : '514'; ?>" 
                                   min="1" max="65535" required>
                            <div class="form-text">Standard port: 514 (UDP) / 601 or 1468 (TCP)</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="protocol" class="form-label">Protocol <span class="text-danger">*</span></label>
                            <select name="protocol" id="protocol" class="form-select" required>
                                <?php
                                $selected_protocol = isset($_POST['protocol']) ? $_POST['protocol'] : 'udp';
                                ?>
                                <option value="udp" <?php echo ($selected_protocol == 'udp') ? 'selected' : ''; ?>>UDP (User Datagram Protocol)</option>
                                <option value="tcp" <?php echo ($selected_protocol == 'tcp') ? 'selected' : ''; ?>>TCP (Transmission Control Protocol)</option>
                            </select>
                            <div class="form-text">UDP: fast, no guarantee / TCP: reliable, with connection</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="facility" class="form-label">Facility <span class="text-danger">*</span></label>
                            <select name="facility" id="facility" class="form-select" required>
                                <?php
                                $facilities = [
                                    0 => 'kern - Kernel messages',
                                    1 => 'user - User-level messages',
                                    2 => 'mail - Mail system',
                                    3 => 'daemon - System daemons',
                                    4 => 'auth - Security/authorization messages',
                                    5 => 'syslog - Messages generated by syslogd',
                                    6 => 'lpr - Line printer subsystem',
                                    7 => 'news - Network news subsystem',
                                    8 => 'uucp - UUCP subsystem',
                                    9 => 'cron - Clock daemon',
                                    10 => 'authpriv - Security/authorization messages (private)',
                                    11 => 'ftp - FTP daemon',
                                    12 => 'ntp - NTP subsystem',
                                    13 => 'security - Log audit',
                                    14 => 'console - Log alert',
                                    15 => 'cron2 - Clock daemon (2)',
                                    16 => 'local0 - Local use 0',
                                    17 => 'local1 - Local use 1',
                                    18 => 'local2 - Local use 2',
                                    19 => 'local3 - Local use 3',
                                    20 => 'local4 - Local use 4',
                                    21 => 'local5 - Local use 5',
                                    22 => 'local6 - Local use 6',
                                    23 => 'local7 - Local use 7'
                                ];
                                
                                $selected_facility = isset($_POST['facility']) ? intval($_POST['facility']) : 16;
                                foreach ($facilities as $value => $label) {
                                    $selected = ($selected_facility == $value) ? 'selected' : '';
                                    echo "<option value='$value' $selected>$label</option>\n";
                                }
                                ?>
                            </select>
                            <div class="form-text">Message source code (0-23)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="severity" class="form-label">Severity <span class="text-danger">*</span></label>
                            <select name="severity" id="severity" class="form-select" required>
                                <?php
                                $severities = [
                                    0 => 'Emergency - System is unusable',
                                    1 => 'Alert - Action must be taken immediately',
                                    2 => 'Critical - Critical conditions',
                                    3 => 'Error - Error conditions',
                                    4 => 'Warning - Warning conditions',
                                    5 => 'Notice - Normal but significant condition',
                                    6 => 'Informational - Informational messages',
                                    7 => 'Debug - Debug-level messages'
                                ];
                                
                                $selected_severity = isset($_POST['severity']) ? intval($_POST['severity']) : 6;
                                foreach ($severities as $value => $label) {
                                    $selected = ($selected_severity == $value) ? 'selected' : '';
                                    echo "<option value='$value' $selected>$label</option>\n";
                                }
                                ?>
                            </select>
                            <div class="form-text">Message severity level (0-7)</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hostname" class="form-label">Source Hostname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="hostname" name="hostname" 
                                   value="<?php echo isset($_POST['hostname']) ? htmlspecialchars($_POST['hostname']) : gethostname(); ?>" 
                                   maxlength="255" required>
                            <div class="form-text">Source hostname of the message (max 255 characters)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="app_name" class="form-label">Application <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="app_name" name="app_name" 
                                   value="<?php echo isset($_POST['app_name']) ? htmlspecialchars($_POST['app_name']) : 'WebApp'; ?>" 
                                   maxlength="48" required pattern="[a-zA-Z0-9_-]+">
                            <div class="form-text">Application name (letters, numbers, _, - only)</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="process_id" class="form-label">Process ID</label>
                            <input type="number" class="form-control" id="process_id" name="process_id" 
                                   value="<?php echo isset($_POST['process_id']) ? htmlspecialchars($_POST['process_id']) : (((getmypid() - 1) % 65535) + 1); ?>" 
                                   min="1" max="65535">
                            <div class="form-text">Process ID (optional, 1-65535)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="timestamp_format" class="form-label">Timestamp Format</label>
                            <select name="timestamp_format" id="timestamp_format" class="form-select">
                                <?php
                                $timestamp_formats = [
                                    'rfc3164' => 'RFC 3164 (Mmm dd HH:mm:ss)',
                                    'rfc5424' => 'RFC 5424 (ISO 8601)',
                                    'custom' => 'Custom (Y-m-d H:i:s)'
                                ];
                                
                                $selected_ts = isset($_POST['timestamp_format']) ? $_POST['timestamp_format'] : 'rfc3164';
                                foreach ($timestamp_formats as $value => $label) {
                                    $selected = ($selected_ts == $value) ? 'selected' : '';
                                    echo "<option value='$value' $selected>$label</option>\n";
                                }
                                ?>
                            </select>
                            <div class="form-text">Timestamp format</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="4" 
                                  maxlength="1024" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : 'Test message from Syslog generator'; ?></textarea>
                        <div class="form-text">
                            Message content (max 1024 characters) - 
                            <span id="charCount">0</span>/1024 characters
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="send_message" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>
                            Send Message
                        </button>
                        
                        <button type="submit" name="preview_message" class="btn btn-secondary">
                            <i class="bi bi-eye me-1"></i>
                            Preview
                        </button>
                    </div>
                </form>

                <?php
                /**
                 * Function to format the timestamp according to the chosen format
                 */
                function formatTimestamp($format) {
                    switch ($format) {
                        case 'rfc3164':
                            return date('M d H:i:s');
                        case 'rfc5424':
                            return date('c'); // ISO 8601
                        case 'custom':
                            return date('Y-m-d H:i:s');
                        default:
                            return date('M d H:i:s');
                    }
                }

                /**
                 * Function to create a Syslog message in RFC 3164 format
                 */
                function createSyslogMessage($facility, $severity, $hostname, $app_name, $process_id, $message, $timestamp_format) {
                    // Calculate priority: Priority = Facility * 8 + Severity
                    $priority = ($facility * 8) + $severity;
                    
                    // Format timestamp
                    $timestamp = formatTimestamp($timestamp_format);
                    
                    // Build tag (APP_NAME[PID])
                    $tag = $app_name;
                    if (!empty($process_id)) {
                        $tag .= '[' . $process_id . ']';
                    }
                    
                    // Build message in RFC 3164 format: <Priority>Timestamp Hostname Tag: Message
                    $syslog_message = '<' . $priority . '>' . $timestamp . ' ' . $hostname . ' ' . $tag . ': ' . $message;
                    
                    return $syslog_message;
                }

                /**
                 * Function to send the Syslog message via UDP or TCP
                 */
                function sendSyslogMessage($server, $port, $message, $protocol = 'udp') {
                    if ($protocol === 'tcp') {
                        // TCP: stream socket
                        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                        
                        if ($socket === false) {
                            return [
                                'success' => false,
                                'error' => 'Unable to create TCP socket: ' . socket_strerror(socket_last_error())
                            ];
                        }
                        
                        // Connect to server
                        $connect = socket_connect($socket, $server, $port);
                        if ($connect === false) {
                            $error = socket_strerror(socket_last_error($socket));
                            socket_close($socket);
                            return [
                                'success' => false,
                                'error' => 'Unable to connect to TCP server: ' . $error
                            ];
                        }
                        
                        // Send message (TCP often requires a line terminator)
                        $message_with_newline = $message . "\n";
                        $result = socket_write($socket, $message_with_newline, strlen($message_with_newline));
                        
                        if ($result === false) {
                            $error = socket_strerror(socket_last_error($socket));
                            socket_close($socket);
                            return [
                                'success' => false,
                                'error' => 'Error sending TCP message: ' . $error
                            ];
                        }
                        
                        socket_close($socket);
                        
                        return [
                            'success' => true,
                            'bytes_sent' => $result
                        ];
                    } else {
                        // UDP: datagram socket
                        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                        
                        if ($socket === false) {
                            return [
                                'success' => false,
                                'error' => 'Unable to create UDP socket: ' . socket_strerror(socket_last_error())
                            ];
                        }
                        
                        // Send message
                        $result = socket_sendto($socket, $message, strlen($message), 0, $server, $port);
                        
                        if ($result === false) {
                            $error = socket_strerror(socket_last_error($socket));
                            socket_close($socket);
                            return [
                                'success' => false,
                                'error' => 'Error sending UDP message: ' . $error
                            ];
                        }
                        
                        socket_close($socket);
                        
                        return [
                            'success' => true,
                            'bytes_sent' => $result
                        ];
                    }
                }

                // Form processing
                if (isset($_POST['send_message']) || isset($_POST['preview_message'])) {
                    $facility = intval($_POST['facility']);
                    $severity = intval($_POST['severity']);
                    $syslog_server = trim($_POST['syslog_server']);
                    $syslog_port = intval($_POST['syslog_port']);
                    $protocol = isset($_POST['protocol']) ? $_POST['protocol'] : 'udp';
                    $hostname = trim($_POST['hostname']);
                    $app_name = trim($_POST['app_name']);
                    $process_id = !empty($_POST['process_id']) ? intval($_POST['process_id']) : '';
                    $message = trim($_POST['message']);
                    $timestamp_format = $_POST['timestamp_format'];
                    
                    // Validation
                    $errors = [];
                    
                    if (strlen($hostname) > 255) {
                        $errors[] = 'Hostname must not exceed 255 characters.';
                    }
                    
                    if (strlen($app_name) > 48) {
                        $errors[] = 'Application name must not exceed 48 characters.';
                    }
                    
                    if (strlen($message) > 1024) {
                        $errors[] = 'Message must not exceed 1024 characters.';
                    }
                    
                    if ($syslog_port < 1 || $syslog_port > 65535) {
                        $errors[] = 'Port must be between 1 and 65535.';
                    }
                    
                    if (empty($errors)) {
                        // Create Syslog message
                        $syslog_message = createSyslogMessage($facility, $severity, $hostname, $app_name, $process_id, $message, $timestamp_format);
                        
                        echo '<div class="mt-4">';
                        
                        // Display preview
                        echo '<div class="card result-box mb-3">';
                        echo '<div class="card-header bg-light">';
                        echo '<strong>Syslog Message Preview</strong>';
                        echo '</div>';
                        echo '<div class="card-body">';
                        
                        // Message information
                        $priority = ($facility * 8) + $severity;
                        echo '<div class="row mb-3">';
                        echo '<div class="col-md-6">';
                        echo '<strong>Priority:</strong> <span class="badge bg-primary info-badge">' . $priority . '</span> ';
                        echo '<small class="text-muted">(Facility: ' . $facility . ' × 8 + Severity: ' . $severity . ')</small>';
                        echo '</div>';
                        echo '<div class="col-md-6">';
                        echo '<strong>Size:</strong> <span class="badge bg-info info-badge">' . strlen($syslog_message) . ' bytes</span>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<div class="row mb-3">';
                        echo '<div class="col-md-12">';
                        echo '<strong>Destination:</strong> ' . htmlspecialchars($syslog_server) . ':' . $syslog_port . ' (' . strtoupper($protocol) . ')';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<strong>Formatted message:</strong>';
                        echo '<div class="syslog-preview">' . htmlspecialchars($syslog_message) . '</div>';
                        echo '</div>';
                        echo '</div>';
                        
                        // Send message if "Send" button was clicked
                        if (isset($_POST['send_message'])) {
                            $result = sendSyslogMessage($syslog_server, $syslog_port, $syslog_message, $protocol);
                            
                            if ($result['success']) {
                                echo '<div class="alert alert-success d-flex align-items-center" role="alert">';
                                echo '<i class="bi bi-check-circle-fill me-2 flex-shrink-0" style="font-size: 1.25rem;"></i>';
                                echo '<div>';
                                echo '<strong>Message sent successfully!</strong><br>';
                                echo 'Server: ' . htmlspecialchars($syslog_server) . ':' . $syslog_port . '<br>';
                                echo 'Bytes sent: ' . $result['bytes_sent'] . '<br>';
                                echo 'Timestamp: ' . date('Y-m-d H:i:s');
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<div class="alert alert-danger d-flex align-items-start" role="alert">';
                                echo '<i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0" style="font-size: 1.25rem;"></i>';
                                echo '<div>';
                                echo '<strong>Error sending message</strong><br>';
                                echo htmlspecialchars($result['error']);
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        
                        echo '</div>';
                    } else {
                        // Display validation errors
                        echo '<div class="alert alert-danger d-flex align-items-start mt-4" role="alert">';
                        echo '<i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0" style="font-size: 1.25rem;"></i>';
                        echo '<div>';
                        echo '<strong>Validation errors:</strong><ul class="mb-0">';
                        foreach ($errors as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul></div>';
                        echo '</div>';
                    }
                }
                ?>
                
                <div class="mt-4">
                    <h2 class="h5">Syslog Documentation</h2>
                    <div class="accordion" id="syslogDocs">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#facilityDocs">
                                    Facilities (0-23)
                                </button>
                            </h2>
                            <div id="facilityDocs" class="accordion-collapse collapse" data-bs-parent="#syslogDocs">
                                <div class="accordion-body">
                                    <p>Facilities identify the message source:</p>
                                    <ul>
                                        <li><strong>0-15:</strong> Standard system facilities</li>
                                        <li><strong>16-23:</strong> Local0 to Local7 (custom usage)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#severityDocs">
                                    Severities (0-7)
                                </button>
                            </h2>
                            <div id="severityDocs" class="accordion-collapse collapse" data-bs-parent="#syslogDocs">
                                <div class="accordion-body">
                                    <p>Severities indicate the severity level:</p>
                                    <ul>
                                        <li><strong>0 - Emergency:</strong> System is unusable</li>
                                        <li><strong>1 - Alert:</strong> Action must be taken immediately</li>
                                        <li><strong>2 - Critical:</strong> Critical conditions</li>
                                        <li><strong>3 - Error:</strong> Error conditions</li>
                                        <li><strong>4 - Warning:</strong> Warning conditions</li>
                                        <li><strong>5 - Notice:</strong> Normal but significant condition</li>
                                        <li><strong>6 - Informational:</strong> Informational messages</li>
                                        <li><strong>7 - Debug:</strong> Debug-level messages</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formatDocs">
                                    Message Format (RFC 3164)
                                </button>
                            </h2>
                            <div id="formatDocs" class="accordion-collapse collapse" data-bs-parent="#syslogDocs">
                                <div class="accordion-body">
                                    <p>Standard format:</p>
                                    <code>&lt;Priority&gt;Timestamp Hostname Tag: Message</code>
                                    <br><br>
                                    <p>Where:</p>
                                    <ul>
                                        <li><strong>Priority:</strong> (Facility × 8) + Severity</li>
                                        <li><strong>Timestamp:</strong> Mmm dd HH:mm:ss (ex: Mar 04 14:30:45)</li>
                                        <li><strong>Hostname:</strong> Source machine name</li>
                                        <li><strong>Tag:</strong> Application[PID]</li>
                                        <li><strong>Message:</strong> Message content (max 1024 characters)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3 text-muted">
            <small>Syslog Message Generator - RFC 3164 & RFC 5424 - <?php echo date('Y'); ?></small>
        </div>
    </div>
    
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Character counter for message
        const messageField = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            const count = messageField.value.length;
            charCount.textContent = count;
            
            if (count > 1024) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-muted');
            } else if (count > 900) {
                charCount.classList.add('text-warning');
                charCount.classList.remove('text-muted', 'text-danger');
            } else {
                charCount.classList.add('text-muted');
                charCount.classList.remove('text-warning', 'text-danger');
            }
        }
        
        messageField.addEventListener('input', updateCharCount);
        
        // Initialize on load
        updateCharCount();
    </script>
</body>
</html>
