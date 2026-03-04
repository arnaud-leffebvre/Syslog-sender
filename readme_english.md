# Syslog Message Generator

A PHP web tool to compose and send Syslog-formatted messages (RFC 3164) to a remote Syslog server via UDP.

## 📋 Requirements

- Web server with PHP 7.0 or higher
- PHP `sockets` extension enabled
- Network connection to target Syslog server
- Modern web browser with Bootstrap 5 support

## 🚀 Installation

1. **Clone or download the files**
   ```bash
   git clone <your-repo>
   cd Syslog_sender
   ```

2. **Check PHP sockets extension**
   ```bash
   php -m | grep sockets
   ```
   
   If the extension is not enabled, edit your `php.ini`:
   ```ini
   extension=sockets
   ```

3. **Deploy to your web server**
   - Place the files in your web directory (e.g., `/var/www/html/` or `C:\xampp\htdocs\`)
   - Ensure PHP has the necessary permissions

4. **Access the application**
   - Open your browser and navigate to: `http://localhost/syslog_sender_english.php`
   - Or use the French version: `http://localhost/syslog_sender.php`

## 📖 Usage

### Basic Configuration

1. **Syslog Server**: Enter the IP address or hostname of your Syslog server
   - Example: `192.168.1.100` or `syslog.example.com`
   - Default: `127.0.0.1` (localhost)

2. **Syslog Port**: Specify the UDP or TCP port of the server
   - Standard UDP port: `514`
   - Common TCP ports: `601`, `1468`
   - Valid range: 1-65535

3. **Protocol**: Choose between UDP and TCP
   - **UDP**: Fast, connectionless, no delivery guarantee (recommended for classic Syslog)
   - **TCP**: Reliable, connection-based, guarantees delivery (recommended for critical logs)

### Message Parameters

#### Facility (0-23)
Identifies the message source:
- **0-15**: Standard system facilities (kern, user, mail, daemon, auth, etc.)
- **16-23**: Local0 to Local7 (custom usage)

Example: Use `local0` (16) for custom applications

#### Severity (0-7)
Indicates the severity level:
- **0 - Emergency**: System is unusable
- **1 - Alert**: Action must be taken immediately
- **2 - Critical**: Critical conditions
- **3 - Error**: Error conditions
- **4 - Warning**: Warning conditions
- **5 - Notice**: Normal but significant condition
- **6 - Informational**: Informational messages (default)
- **7 - Debug**: Debug-level messages

#### Source Hostname
- Hostname of the source machine
- Default value: web server hostname
- Maximum: 255 characters

#### Application
- Name of the sending application
- Format: letters, numbers, underscore (_) and hyphens (-) only
- Maximum: 48 characters
- Example: `WebApp`, `MyService`

#### Process ID (optional)
- ID of the sending process
- Range: 1-65535
- Default value: PID of the current PHP process

#### Timestamp Format
- **RFC 3164**: `Mmm dd HH:mm:ss` (e.g., Mar 04 14:30:45) - Default
- **RFC 5424**: ISO 8601 format (e.g., 2026-03-04T14:30:45+01:00)
- **Custom**: `Y-m-d H:i:s` (e.g., 2026-03-04 14:30:45)

#### Message
- Text content of the message
- Maximum: 1024 characters
- Real-time character counter

### Available Actions

#### Preview
- Displays the formatted message before sending
- Shows the calculated priority: `(Facility × 8) + Severity`
- Displays the size in bytes
- No network transmission performed

#### Send Message
- Previews AND sends the message to the Syslog server
- Displays confirmation with the number of bytes sent
- Shows errors in case of network failure

## 📐 Syslog Message Format (RFC 3164)

```
<Priority>Timestamp Hostname Tag: Message
```

### Generated Message Example

```
<134>Mar 04 14:30:45 webserver WebApp[1234]: Test message from Syslog generator
```

Breakdown:
- **Priority**: `134` = (16 × 8) + 6 = local0 + informational
- **Timestamp**: `Mar 04 14:30:45`
- **Hostname**: `webserver`
- **Tag**: `WebApp[1234]`
- **Message**: `Test message from Syslog generator`

## 🔧 Syslog Server Configuration

### Linux (rsyslog)

**For UDP:**

1. Edit `/etc/rsyslog.conf`:
   ```bash
   # Enable UDP reception
   module(load="imudp")
   input(type="imudp" port="514")
   ```

**For TCP:**

1. Edit `/etc/rsyslog.conf`:
   ```bash
   # Enable TCP reception
   module(load="imtcp")
   input(type="imtcp" port="601")
   ```

2. Restart the service:
   ```bash
   sudo systemctl restart rsyslog
   ```

### Windows

Use a Syslog server such as:
- Kiwi Syslog Server
- Visual Syslog Server
- SolarWinds Syslog Server

## 🛡️ Security

### Best Practices

1. **Access Filtering**: Limit access to the script via `.htaccess` or web server configuration
   ```apache
   <Directory /var/www/html/syslog_sender>
       Require ip 192.168.1.0/24
   </Directory>
   ```

2. **HTTPS**: Use HTTPS to protect data in transit
3. **Validation**: The script automatically validates all fields
4. **XSS Protection**: Uses `htmlspecialchars()` to escape outputs

### Limitations

- **UDP Protocol**: No delivery guarantee (messages can be lost)
- **TCP Protocol**: Requires established connection (may fail if server is unreachable)
- **Size**: Messages limited to 1024 characters (Syslog standard)
- **Encoding**: UTF-8 recommended

## 🐛 Troubleshooting

### Error: "Unable to create socket"
- Check that the `sockets` extension is enabled in PHP
- Check web server permissions

### Error: "Error sending message"
- Check that the Syslog server is accessible (ping, telnet)
- **For UDP**: Check that UDP port 514 is not blocked by a firewall
- **For TCP**: Check that TCP port (601/1468) is not blocked and the server accepts connections
- Check that the Syslog server accepts the chosen protocol (UDP or TCP)

### Messages not received
- Check Syslog server configuration (UDP/TCP enabled according to your choice)
- Check Syslog server filtering rules
- Check Syslog server logs
- **UDP**: Messages can be lost during network congestion
- **TCP**: Check that the connection is properly established

### Connectivity test
```bash
# UDP test - Linux/Mac
nc -u -v <syslog_server> 514

# TCP test - Linux/Mac
nc -v <syslog_server> 601

# Windows (PowerShell) - UDP
Test-NetConnection -ComputerName <syslog_server> -Port 514

# Windows (PowerShell) - TCP
Test-NetConnection -ComputerName <syslog_server> -Port 601
```

## 📚 References

- [RFC 3164 - The BSD syslog Protocol](https://tools.ietf.org/html/rfc3164)
- [RFC 5424 - The Syslog Protocol](https://tools.ietf.org/html/rfc5424)
- [PHP Sockets Documentation](https://www.php.net/manual/en/book.sockets.php)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)

## 📄 License

This project is provided for educational and testing purposes. Use at your own risk.

## 🌐 Language Versions

- **Français**: `syslog_sender.php` + `readme.md`
- **English**: `syslog_sender_english.php` + `readme.en`

## 👨‍💻 Support

For any questions or issues:
1. Check PHP logs: `/var/log/apache2/error.log` or `C:\xampp\apache\logs\error.log`
2. Enable PHP debug mode (development only)
3. Consult your server's Syslog documentation

---

**Version**: 1.0  
**Date**: March 2026  
**Technologies**: PHP, Bootstrap 5, Bootstrap Icons
