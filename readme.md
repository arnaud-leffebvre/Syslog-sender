# Générateur de Messages Syslog

Un outil web PHP pour composer et envoyer des messages au format Syslog (RFC 3164) vers un serveur Syslog distant via UDP ou TCP.

## 📋 Prérequis

- Serveur web avec PHP 7.0 ou supérieur
- Extension PHP `sockets` activée
- Connexion réseau vers le serveur Syslog cible
- Navigateur web moderne avec support Bootstrap 5

## 🚀 Installation

1. **Cloner ou télécharger les fichiers**
   ```bash
   git clone <votre-repo>
   cd Syslog_sender
   ```

2. **Vérifier l'extension PHP sockets**
   ```bash
   php -m | grep sockets
   ```
   
   Si l'extension n'est pas activée, éditez votre `php.ini` :
   ```ini
   extension=sockets
   ```

3. **Déployer sur votre serveur web**
   - Placez les fichiers dans votre répertoire web (ex: `/var/www/html/` ou `C:\xampp\htdocs\`)
   - Assurez-vous que PHP a les permissions nécessaires

4. **Accéder à l'application**
   - Ouvrez votre navigateur et accédez à : `http://localhost/syslog_sender.php`
   - Ou utilisez la version anglaise : `http://localhost/syslog_sender_english.php`

## 📖 Utilisation

### Configuration de base

1. **Serveur Syslog** : Entrez l'adresse IP ou le nom d'hôte de votre serveur Syslog
   - Exemple : `192.168.1.100` ou `syslog.example.com`
   - Par défaut : `127.0.0.1` (localhost)

2. **Port Syslog** : Spécifiez le port UDP ou TCP du serveur
   - Port UDP standard : `514`
   - Ports TCP courants : `601`, `1468`
   - Plage valide : 1-65535

3. **Protocole** : Choisissez entre UDP et TCP
   - **UDP** : Rapide, sans connexion, sans garantie de livraison (recommandé pour Syslog classique)
   - **TCP** : Fiable, avec connexion, garantit la livraison (recommandé pour logs critiques)

### Paramètres du message

#### Facility (0-23)
Identifie la source du message :
- **0-15** : Facilities système standard (kern, user, mail, daemon, auth, etc.)
- **16-23** : Local0 à Local7 (usage personnalisé)

Exemple : Utilisez `local0` (16) pour des applications personnalisées

#### Severity (0-7)
Indique le niveau de gravité :
- **0 - Emergency** : Système inutilisable
- **1 - Alert** : Action immédiate requise
- **2 - Critical** : Condition critique
- **3 - Error** : Condition d'erreur
- **4 - Warning** : Condition d'avertissement
- **5 - Notice** : Condition normale mais significative
- **6 - Informational** : Message informatif (par défaut)
- **7 - Debug** : Message de débogage

#### Hostname Source
- Nom d'hôte de la machine source
- Valeur par défaut : nom d'hôte du serveur web
- Maximum : 255 caractères

#### Application
- Nom de l'application émettrice
- Format : lettres, chiffres, underscore (_) et tirets (-) uniquement
- Maximum : 48 caractères
- Exemple : `WebApp`, `MyService`

#### Process ID (optionnel)
- ID du processus émetteur
- Plage : 1-65535
- Valeur par défaut : PID du processus PHP actuel

#### Format Timestamp
- **RFC 3164** : `Mmm dd HH:mm:ss` (ex: Mar 04 14:30:45) - Par défaut
- **RFC 5424** : Format ISO 8601 (ex: 2026-03-04T14:30:45+01:00)
- **Personnalisé** : `Y-m-d H:i:s` (ex: 2026-03-04 14:30:45)

#### Message
- Contenu textuel du message
- Maximum : 1024 caractères
- Compteur de caractères en temps réel

### Actions disponibles

#### Prévisualiser
- Affiche le message formaté avant l'envoi
- Montre la priorité calculée : `(Facility × 8) + Severity`
- Affiche la taille en octets
- Aucun envoi réseau effectué

#### Envoyer le message
- Prévisualise ET envoie le message au serveur Syslog
- Affiche une confirmation avec le nombre d'octets envoyés
- Affiche les erreurs en cas d'échec réseau

## 📐 Format du message Syslog (RFC 3164)

```
<Priority>Timestamp Hostname Tag: Message
```

### Exemple de message généré

```
<134>Mar 04 14:30:45 webserver WebApp[1234]: Test message from Syslog generator
```

Décomposition :
- **Priority** : `134` = (16 × 8) + 6 = local0 + informational
- **Timestamp** : `Mar 04 14:30:45`
- **Hostname** : `webserver`
- **Tag** : `WebApp[1234]`
- **Message** : `Test message from Syslog generator`

## 🔧 Configuration du serveur Syslog

### Linux (rsyslog)

**Pour UDP :**

1. Éditer `/etc/rsyslog.conf` :
   ```bash
   # Activer la réception UDP
   module(load="imudp")
   input(type="imudp" port="514")
   ```

**Pour TCP :**

1. Éditer `/etc/rsyslog.conf` :
   ```bash
   # Activer la réception TCP
   module(load="imtcp")
   input(type="imtcp" port="601")
   ```

2. Redémarrer le service :
   ```bash
   sudo systemctl restart rsyslog
   ```

### Windows

Utilisez un serveur Syslog tel que :
- Kiwi Syslog Server
- Visual Syslog Server
- SolarWinds Syslog Server

## 🛡️ Sécurité

### Bonnes pratiques

1. **Filtrage d'accès** : Limitez l'accès au script via `.htaccess` ou configuration web server
   ```apache
   <Directory /var/www/html/syslog_sender>
       Require ip 192.168.1.0/24
   </Directory>
   ```

2. **HTTPS** : Utilisez HTTPS pour protéger les données en transit
3. **Validation** : Le script valide automatiquement tous les champs
4. **Protection XSS** : Utilise `htmlspecialchars()` pour échapper les sorties

### Limitations

- **Protocole UDP** : Aucune garantie de livraison (messages peuvent être perdus)
- **Protocole TCP** : Nécessite une connexion établie (peut échouer si le serveur est inaccessible)
- **Taille** : Messages limités à 1024 caractères (standard Syslog)
- **Encodage** : UTF-8 recommandé

## 🐛 Dépannage

### Erreur : "Impossible de créer le socket"
- Vérifiez que l'extension `sockets` est activée dans PHP
- Vérifiez les permissions du serveur web

### Erreur : "Erreur lors de l'envoi"
- Vérifiez que le serveur Syslog est accessible (ping, telnet)
- **Pour UDP** : Vérifiez que le port UDP 514 n'est pas bloqué par un firewall
- **Pour TCP** : Vérifiez que le port TCP (601/1468) n'est pas bloqué et que le serveur accepte les connexions
- Vérifiez que le serveur Syslog accepte le protocole choisi (UDP ou TCP)

### Messages non reçus
- Vérifiez la configuration du serveur Syslog (UDP/TCP activé selon votre choix)
- Vérifiez les règles de filtrage du serveur Syslog
- Vérifiez les logs du serveur Syslog
- **UDP** : Les messages peuvent être perdus en cas de congestion réseau
- **TCP** : Vérifiez que la connexion est bien établie

### Test de connectivité
```bash
# Test UDP - Linux/Mac
nc -u -v <serveur_syslog> 514

# Test TCP - Linux/Mac
nc -v <serveur_syslog> 601

# Windows (PowerShell) - UDP
Test-NetConnection -ComputerName <serveur_syslog> -Port 514

# Windows (PowerShell) - TCP
Test-NetConnection -ComputerName <serveur_syslog> -Port 601
```

## 📚 Références

- [RFC 3164 - The BSD syslog Protocol](https://tools.ietf.org/html/rfc3164)
- [RFC 5424 - The Syslog Protocol](https://tools.ietf.org/html/rfc5424)
- [Documentation PHP Sockets](https://www.php.net/manual/fr/book.sockets.php)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)

## 📄 Licence

Ce projet est fourni à des fins éducatives et de test. Utilisez-le à vos propres risques.

## 🌐 Versions linguistiques

- **Français** : `syslog_sender.php` + `readme.md`
- **English** : `syslog_sender_english.php` + `readme.en`

## 👨‍💻 Support

Pour toute question ou problème :
1. Vérifiez les logs PHP : `/var/log/apache2/error.log` ou `C:\xampp\apache\logs\error.log`
2. Activez le mode debug PHP (développement uniquement)
3. Consultez la documentation Syslog de votre serveur

---

**Version** : 1.0  
**Date** : Mars 2026  
**Technologies** : PHP, Bootstrap 5, Bootstrap Icons

