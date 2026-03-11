# MODO – Demo-Webshop für Usability-Tests

## Gewählter Stack und Begründung

**Stack:** PHP 8.x + Flat JSON (kein Framework, kein Build-Tool)

**Begründung:**
- **Maximale Portabilität:** Läuft auf jedem Shared Hosting mit Apache/PHP – kein Node.js, kein Composer, kein Build-Prozess erforderlich.
- **Null Abhängigkeiten:** Kein Framework-Lock-in, keine externen Libraries. Der Code ist sofort deployfähig.
- **Einfache Wartung:** JSON-Dateien als Datenspeicher statt Datenbank – kein SQL-Setup, Backups sind einfache Dateikopien.
- **Schnelles Onboarding:** Neue Entwickler können den Code ohne Framework-Wissen verstehen und ändern.
- **Ausreichend für den Zweck:** Für einen Demo-Shop mit ~16 Produkten ist eine SQL-Datenbank Overkill.

## Projektstruktur

```
ecom-demo/
├── index.php                # Haupt-Router (URL-Dispatch)
├── api.php                  # REST-API für AJAX-Aufrufe
├── .htaccess                # Apache URL-Rewriting
├── CLAUDE.md                # Diese Dokumentation
├── assets/
│   ├── css/
│   │   └── style.css        # Komplettes Stylesheet (mobile-first)
│   ├── js/
│   │   └── app.js           # Cart-Logik, Toasts, Utilities
│   └── images/              # Hochgeladene Logos etc.
├── data/
│   ├── products.json        # Produktdaten (inkl. Variationen)
│   └── settings.json        # Shop-Einstellungen (persistent)
├── includes/
│   └── functions.php        # Datenzugriff, Hilfsfunktionen
├── templates/
│   ├── layout.php           # HTML-Rahmen (Header, Footer, Nav)
│   ├── home.php             # Produktliste mit Filter/Suche
│   ├── product.php          # Produktdetailseite
│   ├── cart.php             # Warenkorb
│   ├── checkout.php         # 3-stufiger Checkout
│   ├── confirmation.php     # Bestellbestätigung
│   ├── settings.php         # Admin-Einstellungen
│   └── 404.php              # Fehlerseite
├── sample_products.csv      # Originale Quelldaten
└── sample_products.xml      # Originale Quelldaten (XML)
```

## Setup-Anleitung

### Lokal starten

**Mit PHP Built-in Server (empfohlen für Entwicklung):**
```bash
cd ecom-demo
php -S localhost:8000
```
Dann im Browser: `http://localhost:8000`

**Hinweis:** Der Built-in Server unterstützt kein `.htaccess`. Das Routing funktioniert trotzdem, da `index.php` als Einstiegspunkt den `route`-Parameter verarbeitet. Für saubere URLs ohne `?route=` wird Apache mit mod_rewrite benötigt.

### Auf Server deployen

1. Alle Dateien per FTP/SFTP ins Web-Root hochladen
2. Sicherstellen, dass Apache `mod_rewrite` aktiviert ist
3. Sicherstellen, dass das Verzeichnis `data/` beschreibbar ist: `chmod 755 data/`
4. Sicherstellen, dass `assets/images/` beschreibbar ist (für Logo-Upload): `chmod 755 assets/images/`
5. Fertig – kein Build, kein Install, kein Setup

### Nginx-Alternative

Falls Nginx statt Apache verwendet wird, diese Konfiguration nutzen:
```nginx
location / {
    try_files $uri $uri/ /index.php?route=$uri&$args;
}
```

## Datenstruktur

### products.json

Jedes Produkt hat folgende Struktur:
```json
{
  "id": 1,
  "sku": "woo-vneck-tee",
  "name": "V-Neck T-Shirt",
  "slug": "v-neck-t-shirt",
  "type": "simple|variable",
  "short_description": "Kurzbeschreibung",
  "description": "Langbeschreibung",
  "regular_price": 20.00,
  "sale_price": null,
  "category": "T-Shirts",
  "in_stock": true,
  "stock_qty": 45,
  "images": ["url1", "url2"],
  "attributes": [
    {"name": "Farbe", "options": ["Rot", "Grün", "Blau"]},
    {"name": "Grösse", "options": ["S", "M", "L"]}
  ],
  "variations": [
    {
      "id": 101,
      "attributes": {"Farbe": "Rot", "Grösse": "S"},
      "price": 20.00,
      "stock_qty": 8,
      "image": "url"
    }
  ],
  "featured": true
}
```

### settings.json

```json
{
  "shop_name": "MODO",
  "slogan": "Dein Style, deine Wahl",
  "primary_color": "#1a1a2e",
  "secondary_color": "#e94560",
  "font_family": "Inter",
  "shipping_cost": 7.90,
  "free_shipping_threshold": 75.00,
  "vat_rate": 8.1,
  "payment_methods": {
    "twint": true,
    "credit_card": true,
    "invoice": true
  }
}
```

## Produkte und Variationen

Die Produktdaten wurden aus `sample_products.csv` extrahiert und in `data/products.json` überführt. Die Daten enthalten:

- **16 Produkte** in 4 Kategorien (T-Shirts, Hoodies, Accessoires, Musik)
- **2 variable Produkte** mit Variationen (V-Neck T-Shirt, Hoodie)
- **14 einfache Produkte** mit festem Preis
- Alle Texte sind auf Deutsch (CH) übersetzt
- Produktbilder werden direkt von den Original-URLs eingebunden

### Variationen

Variable Produkte (z.B. V-Neck T-Shirt) haben:
- `attributes`: Die verfügbaren Attribute (Farbe, Grösse)
- `variations`: Jede mögliche Kombination mit eigenem Preis, Bestand und Bild

Die Variantenauswahl auf der Produktdetailseite:
- **Farben:** Farbige Kreise (Swatches)
- **Grössen/Logo:** Textbuttons
- Preis und Lagerbestand aktualisieren sich live bei Auswahl

## /settings-Konfiguration

Die Einstellungsseite (`/settings`) ist ohne Login zugänglich und bietet vier Bereiche:

1. **Erscheinungsbild:** Farben, Schriftart (Google Fonts), Schriftgrössen, Logo-Upload
2. **Shop-Infos:** Name, Slogan, Kontaktdaten, Land/Währung
3. **Produkte:** Preise, Aktionspreise, Bestand direkt bearbeiten; Variationen verwalten
4. **Checkout:** Versandkosten, MwSt.-Satz, Zahlungsmittel ein-/ausschalten

Alle Änderungen werden sofort in `data/settings.json` gespeichert und wirken sich beim nächsten Seitenaufruf aus.

## Hinweise für Usability-Probleme (künftige Iterationen)

Im Code sind `// USABILITY-HOOK`-Kommentare an den Stellen platziert, an denen sich gezielte Usability-Probleme einbauen lassen. Hier einige Ideen:

### Navigation & Suche
- **Header-Navigation:** Kategorien umbenennen oder verstecken
- **Suchfunktion:** Suchfeld verzögern, Ergebnisse unsortiert anzeigen, Autovervollständigung deaktivieren
- **Breadcrumb:** Entfernen oder fehlerhaft machen

### Produkte
- **Produktraster:** Unterschiedliche Kartengrössen, fehlende Bilder
- **Bildergalerie:** Thumbnail-Klick verzögern, Bilder in falscher Reihenfolge
- **Variantenauswahl:** Ausgewählte Variante nicht klar markieren, Preisupdate verzögern
- **Lagerstatus:** Falsche Bestände anzeigen, Status verstecken

### Warenkorb
- **Warenkorb-Anzeige:** Mengenänderung umständlich machen, Entfernen-Button schwer findbar
- **Warenkorb-Icon:** Badge nicht aktualisieren, falsche Zahl anzeigen

### Checkout
- **Schrittanzeige:** Aktiven Schritt nicht klar markieren, Rücknavigation entfernen
- **Adressformular:** PLZ-Validierung zu strikt, Pflichtfelder nicht markiert, Fehlermeldungen unklar
- **Zahlungsauswahl:** Zahlungsmethoden nicht klar beschrieben, QR-Code zu klein
- **Kreditkartenformular:** Luhn-Validierung entfernen (akzeptiert alles), Kartentyp nicht erkennen
- **Checkout-Button:** Farbe ändern, Position verschieben, deaktiviert erscheinen lassen

### Allgemein
- **Formularvalidierung:** Fehlermeldungen erst nach Submit statt inline
- **Loading-States:** Spinner entfernen, falsche Ladezeiten simulieren
- **Mobile:** Touch-Targets zu klein machen, Scroll-Probleme einbauen
