# MODO Demo-Webshop – Installationsanleitung

## Voraussetzungen

- **PHP 8.0 oder höher** muss installiert sein
- **Git** muss installiert sein

### PHP installieren (falls noch nicht vorhanden)

**macOS:**
```bash
brew install php
```

**Windows:**
- [PHP für Windows herunterladen](https://windows.php.net/download/) oder
- [XAMPP installieren](https://www.apachefriends.org/) (enthält PHP + Apache)

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php php-cli php-json
```

**PHP-Version prüfen:**
```bash
php -v
```

---

## Installation

### 1. Repository klonen

```bash
git clone https://github.com/honegglu/ecom-demo.git
```

### 2. In das Projektverzeichnis wechseln

```bash
cd ecom-demo
```

### 3. Lokalen Server starten

```bash
php -S localhost:8000
```

### 4. Im Browser öffnen

```
http://localhost:8000
```

Fertig – der Shop läuft.

---

## Wichtige URLs

| Seite | URL |
|---|---|
| Startseite / Produktliste | http://localhost:8000 |
| Warenkorb | http://localhost:8000/?route=/cart |
| Checkout | http://localhost:8000/?route=/checkout |
| Admin-Einstellungen | http://localhost:8000/?route=/settings |
| Einzelnes Produkt (Beispiel) | http://localhost:8000/?route=/product/v-neck-t-shirt |

> **Hinweis:** Der PHP Built-in Server unterstützt kein `.htaccess`. Deshalb werden URLs mit `?route=` aufgerufen. Auf einem Apache-Server mit `mod_rewrite` funktionieren saubere URLs wie `/cart`, `/settings` etc. direkt.

---

## Ordnerberechtigungen prüfen

Falls Einstellungen nicht gespeichert werden können:

```bash
chmod 755 data/
chmod 755 assets/images/
```

---

## Fehlerbehebung

| Problem | Lösung |
|---|---|
| `php: command not found` | PHP ist nicht installiert – siehe oben |
| Weisse Seite / 500-Fehler | `php -l index.php` ausführen, um Syntaxfehler zu prüfen |
| Einstellungen werden nicht gespeichert | Schreibrechte auf `data/` prüfen: `chmod 755 data/` |
| Logo-Upload funktioniert nicht | Schreibrechte auf `assets/images/` prüfen |
| Port 8000 belegt | Anderen Port verwenden: `php -S localhost:8080` |

---

## Deployment auf einem Server (optional)

Für den Einsatz auf einem echten Webserver (z.B. für Usability-Tests mit Teilnehmern):

1. Dateien per FTP/SFTP auf den Server hochladen
2. Apache `mod_rewrite` muss aktiviert sein
3. `data/` und `assets/images/` müssen beschreibbar sein
4. Kein Build-Schritt, kein `npm install`, kein Composer – einfach hochladen und loslegen
