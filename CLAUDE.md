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
├── api.php                  # REST-API für AJAX-Aufrufe (10 Endpoints)
├── .htaccess                # Apache URL-Rewriting
├── docker-compose.yml       # Docker-Setup (PHP 8.3 + Apache)
├── INSTALLATION.md          # Installationsanleitung
├── CLAUDE.md                # Diese Dokumentation
├── assets/
│   ├── css/
│   │   └── style.css        # Komplettes Stylesheet (mobile-first, ~1400 Zeilen)
│   ├── js/
│   │   └── app.js           # Cart-Logik, Toasts, Utilities
│   └── images/              # Hochgeladene Logos & Produktbilder
├── data/
│   ├── products.json        # Produktdaten (16 Produkte, inkl. Variationen)
│   └── settings.json        # Shop-Einstellungen (persistent)
├── includes/
│   └── functions.php        # Datenzugriff, Hilfsfunktionen (13 Funktionen)
├── templates/
│   ├── layout.php           # HTML-Rahmen (Header, Footer, Nav, Theming)
│   ├── home.php             # Produktliste mit Filter/Suche/Sortierung
│   ├── product.php          # Produktdetailseite mit Varianten & Galerie
│   ├── cart.php             # Warenkorb mit Mengensteuerung
│   ├── checkout.php         # 3-stufiger Checkout (Adresse → Zahlung → Bestätigung)
│   ├── confirmation.php     # Bestellbestätigung mit Zusammenfassung
│   ├── settings.php         # Admin-Einstellungen (4 Tabs)
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

### Mit Docker (empfohlen für saubere URLs)

```bash
docker compose up -d
```
Dann im Browser: `http://localhost:8000`

Docker-Setup nutzt PHP 8.3 + Apache mit mod_rewrite, sodass saubere URLs (`/cart`, `/product/hoodie`) sofort funktionieren. Der Container erstellt automatisch die benötigten Verzeichnisse (`data/`, `assets/images/`) mit korrekten Berechtigungen.

### Auf Server deployen

1. Alle Dateien per FTP/SFTP ins Web-Root hochladen
2. Sicherstellen, dass Apache `mod_rewrite` aktiviert ist
3. Sicherstellen, dass das Verzeichnis `data/` beschreibbar ist: `chmod 755 data/`
4. Sicherstellen, dass `assets/images/` beschreibbar ist (für Logo-/Bild-Upload): `chmod 755 assets/images/`
5. Fertig – kein Build, kein Install, kein Setup

### Nginx-Alternative

Falls Nginx statt Apache verwendet wird, diese Konfiguration nutzen:
```nginx
location / {
    try_files $uri $uri/ /index.php?route=$uri&$args;
}
```

## URL-Routing

Das Routing erfolgt über `index.php` mit einfachem URL-Dispatch via `$_GET['route']`. Apache `.htaccess` leitet alle Anfragen (ausser existierende Dateien) an `index.php` weiter.

| Route | Template | Beschreibung |
|-------|----------|--------------|
| `/` oder `home` | `home.php` | Produktliste mit Filter, Suche, Sortierung |
| `/product/{slug}` | `product.php` | Produktdetailseite |
| `/cart` | `cart.php` | Warenkorb |
| `/checkout` | `checkout.php` | 3-stufiger Checkout-Prozess |
| `/confirmation` | `confirmation.php` | Bestellbestätigung |
| `/settings` | `settings.php` | Admin-Einstellungsseite |
| Alles andere | `404.php` | Fehlerseite (HTTP 404) |

## REST-API (api.php)

Alle API-Aufrufe gehen an `/api.php?action=[action]` und liefern JSON zurück.

### Produkt-Endpoints

| Action | Methode | Beschreibung | Parameter |
|--------|---------|--------------|-----------|
| `get_products` | GET | Alle Produkte (mit Filter) | `category`, `search`, `sort` (name, price_asc, price_desc) |
| `get_product` | GET | Einzelnes Produkt | `slug` |
| `save_product` | POST | Produkt aktualisieren | JSON: `id`, `name`, `regular_price`, `sale_price`, `stock_qty`, `in_stock`, `short_description`, `variations` |

### Einstellungs-Endpoints

| Action | Methode | Beschreibung |
|--------|---------|--------------|
| `get_settings` | GET | Shop-Einstellungen abrufen |
| `save_settings` | POST | Shop-Einstellungen speichern (JSON-Body) |

### Upload-Endpoints

| Action | Methode | Beschreibung | Details |
|--------|---------|--------------|---------|
| `upload_logo` | POST (multipart) | Shop-Logo hochladen | PNG, JPEG, SVG, WebP. Speichert als `logo_[timestamp].[ext]` |
| `upload_product_image` | POST (multipart) | Produktbild hochladen | PNG, JPEG, WebP, GIF. Server-seitige Optimierung mit GD: max 1200px, JPEG 82% |
| `delete_product_image` | POST | Produktbild löschen | JSON: `product_id`, `image_url`. Löscht lokale Datei |

### Bestell-Endpoint

| Action | Methode | Beschreibung | Details |
|--------|---------|--------------|---------|
| `place_order` | POST | Bestellung aufgeben (simuliert) | Gibt `order_number` zurück (Format: `MO-YYYYMMDD-[6-hex]`) |

## Hilfsfunktionen (includes/functions.php)

### Datenzugriff
- `get_settings()` / `save_settings(array)` – Lesen/Schreiben von `data/settings.json`
- `get_products()` / `save_products(array)` – Lesen/Schreiben von `data/products.json`
- `get_product_by_slug(string)` – Produkt nach URL-Slug finden
- `get_product_by_id(int)` – Produkt nach ID finden
- `get_categories()` – Eindeutige Kategorien extrahieren, sortiert

### Preisberechnung
- `get_effective_price(array)` – Gibt `sale_price` zurück (falls vorhanden), sonst `regular_price`
- `get_price_range(array)` – Min/Max-Preis inkl. Variationen (für variable Produkte)
- `format_price(float, ?array)` – Formatiert Preis mit Währung (z.B. `CHF 99.90` oder `CHF 1'234.56`)

### Bestellungen & Berechnung
- `generate_order_number()` – Erzeugt eindeutige Bestellnummer (`MO-20260325-A1B2C3`)
- `calculate_vat(float, float)` – MwSt.-Berechnung
- `calculate_shipping(float, array)` – Versandkosten (gratis ab Schwellenwert)

### Zahlungsvalidierung
- `luhn_check(string)` – Kreditkartennummer mit Luhn-Algorithmus validieren
- `detect_card_type(string)` – Kartentyp erkennen (Visa, Mastercard, Amex)

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
  "font_size_base": "16",
  "font_size_heading": "28",
  "logo_url": "",
  "contact_email": "",
  "contact_phone": "",
  "contact_address": "",
  "country": "CH",
  "currency": "CHF",
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
- **Farben:** Farbige Kreise (Swatches) mit CSS-Hintergrundfarbe
- **Grössen/Logo:** Textbuttons
- Preis und Lagerbestand aktualisieren sich live bei Auswahl
- Bild wechselt automatisch zur gewählten Variante

## Seiten im Detail

### Startseite (home.php)
- Breadcrumb-Navigation
- Produktraster mit responsiven Spalten
- **Filterung:** Nach Kategorie (über Navigation) und Suchbegriff
- **Sortierung:** Name A-Z, Preis aufsteigend/absteigend
- **Produktkarten:** Bild (lazy-loaded), Kategorie-Label, Titel, Kurzbeschreibung, Preis (normal/Sale/Preisbereich), Farb-Swatches
- **Badges:** „Sale" (bei Aktionspreis), „Beliebt" (bei `featured: true`)
- Leerer Zustand mit Illustration wenn keine Produkte gefunden

### Produktdetailseite (product.php)
- **Bildergalerie:** Hauptbild + Thumbnails, Klick wechselt Hauptbild
- Sale-Badge auf dem Hauptbild
- Kategorie, Titel, Kurzbeschreibung
- **Preisanzeige:** Einzelpreis, Preisbereich (variable), Sale mit Ersparnis
- **Variantenauswahl:** Farb-Swatches + Text-Buttons, aktualisiert Preis/Bestand/Bild live
- **Lagerstatus:** Grüner Haken (auf Lager), Warnung (≤5 Stück), Rot (nicht verfügbar)
- **Mengenauswahl:** +/- Buttons (min 1, max 99)
- **In den Warenkorb:** Button mit Erfolgsanimation (Häkchen)
- Info-Boxen: Lieferzeit, Gratis-Versand ab Schwellenwert, 30-Tage-Rückgabe
- Aufklappbare Details (Beschreibung, Versand & Rückgabe)

### Warenkorb (cart.php)
- Leerer Zustand mit Illustration und „Weiter einkaufen"-Link
- **Warenkorb-Tabelle:** Produktbild, Name (verlinkt), Attribute, Mengensteuerung (+/-), Stückpreis, Zeilensumme, Entfernen-Button
- **Zusammenfassung:** Zwischensumme, Versandkosten, MwSt., Gesamtsumme
- Hinweis auf Gratis-Versand (zeigt fehlenden Betrag)
- Buttons: „Zur Kasse" und „Weiter einkaufen"

### Checkout (checkout.php)
3-stufiger Checkout-Wizard mit Schrittanzeige:

**Schritt 1 – Lieferadresse:**
- Formular: Vorname, Nachname, E-Mail, Telefon, Strasse, Adresszusatz, PLZ (4-stellig, CH-Validierung), Ort, Kanton (26 Schweizer Kantone)
- Checkbox: „Rechnungsadresse = Lieferadresse"

**Schritt 2 – Zahlungsmethode:**
- **TWINT:** QR-Code-Anzeige + „In App bestätigen"-Button (simuliert 2.5s Wartezeit)
- **Kreditkarte:** Karteninhaber, Kartennummer (Auto-Formatierung XXXX XXXX XXXX XXXX), Ablaufdatum (MM/JJ), CVV. Luhn-Validierung, automatische Kartenerkennung (Visa/MC/Amex). Simuliert 3D-Secure-Modal (3s Wartezeit)
- **Rechnung:** Geburtsdatum-Eingabe (max. 2008) für Bonitätsprüfung

**Bestellübersicht:** Sidebar mit Artikeln, Zwischensumme, Versand, MwSt., Gesamtsumme

### Bestellbestätigung (confirmation.php)
- Erfolgsicon mit Häkchen
- Bestelldetails: Bestellnummer, Zahlungsmethode, Lieferadresse
- Bestellte Artikel mit Bildern, Namen, Attributen, Mengen
- Kostenübersicht (Zwischensumme, Versand, MwSt., Total)
- E-Mail-Bestätigungshinweis
- „Weiter einkaufen"-Button
- Daten aus `sessionStorage`, wird nach Anzeige gelöscht

### 404-Fehlerseite (404.php)
- Grosse „404"-Anzeige mit Fehlermeldung und Link zurück zum Shop

## /settings-Konfiguration

Die Einstellungsseite (`/settings`) ist ohne Login zugänglich und bietet vier Tabs:

### Tab 1: Erscheinungsbild
- **Primär- und Sekundärfarbe:** HTML5-Farbwähler + Text-Input (synchronisiert)
- **Schriftart:** Dropdown mit 10 Google Fonts (Inter, Roboto, Open Sans, Lato, Montserrat, Poppins, Nunito, Raleway, Source Sans 3, Work Sans)
- **Schriftgrössen:** Slider für Basis- und Überschriftengrösse
- **Logo-Upload:** Dateiauswahl mit Vorschau, unterstützt PNG, JPEG, SVG, WebP

### Tab 2: Shop-Infos
- Shop-Name, Slogan
- Kontakt: E-Mail, Telefon, Adresse
- Land (CH, DE, AT) und Währung (CHF, EUR)

### Tab 3: Produkte (Tabellen-Editor mit Inline-Bearbeitung)
- **Tabelle** mit Spalten: Bilder, Name, Kategorie, Preis, Aktionspreis, Bestand, Aktion
- **Bildverwaltung pro Produkt:**
  - Thumbnail-Galerie der aktuellen Bilder
  - Löschen-Button (×) pro Thumbnail
  - Upload-Button öffnet den integrierten Bildeditor
- **Inline-Bearbeitung:** Name, Preis, Aktionspreis, Bestand direkt in der Tabelle editierbar
- **Speichern-Button** pro Produkt
- **Varianten-Toggle:** Aufklappbare Varianten-Tabelle (Attribute, Preis, Bestand editierbar)

#### Integrierter Bildeditor
- Canvas-Vorschau (max 500px Anzeigegrösse)
- **Drehen:** ±90°-Buttons
- **Zuschnitt:** Frei, 1:1, 4:3, 16:9 (mit Drittel-Raster-Overlay)
- **Zuschnitt-Steuerung:** Maus-Drag zum Verschieben oder Ändern der Grösse (hält Seitenverhältnis bei fixem Ratio)
- **Max. Breite:** Slider (200–1600px)
- **Qualität:** Slider (30–100% JPEG-Kompression)
- **Dateiinfo:** Originalmasse, Ausgabemasse, Qualität
- Server-seitige Optimierung mit PHP GD: max 1200px Breite, 82% JPEG-Qualität

### Tab 4: Checkout-Einstellungen
- Versandkosten (CHF)
- Gratis-Versand ab Betrag (CHF)
- MwSt.-Satz (%)
- Zahlungsmethoden ein-/ausschalten: TWINT, Kreditkarte, Rechnung

Alle Änderungen werden sofort in `data/settings.json` bzw. `data/products.json` gespeichert und wirken sich beim nächsten Seitenaufruf aus.

## Client-seitige Logik (assets/js/app.js)

### Warenkorb (localStorage)
- **Speicherung:** `localStorage` unter Schlüssel `modo_cart`
- `Cart.add(item)` – Artikel hinzufügen (erkennt Duplikate anhand `productId` + `variationId` + `attributes`)
- `Cart.updateQty(index, delta)` – Menge ändern (min 1)
- `Cart.remove(index)` – Artikel entfernen
- `Cart.clear()` – Warenkorb leeren
- `Cart.getCount()` / `Cart.getTotal()` – Anzahl / Gesamtsumme
- `Cart.updateBadge()` / `Cart.animateBadge()` – Badge im Header aktualisieren mit Puls-Animation

### Warenkorb-Artikel-Struktur
```javascript
{
  productId: 1,
  variationId: null | 101,
  name: "V-Neck T-Shirt",
  slug: "v-neck-t-shirt",
  image: "https://...",
  price: 20.00,
  attributes: { "Farbe": "Rot", "Grösse": "M" },
  qty: 2
}
```

### Benachrichtigungen
- `showToast(message, type)` – Toast-Nachricht (Typen: neutral, `success`, `warning`, `error`). Auto-Hide nach 2.5s

### Utilities
- `formatPrice(amount)` – Preis mit Währung aus `window.SHOP_SETTINGS.currency` formatieren
- `escapeHtml(str)` – XSS-Schutz via `textContent`-Trick

### Suche
- Auto-Befüllung des Suchfelds aus URL-Parameter `?search=...`
- Formular-Submit leitet an `/?search=[query]` weiter

## CSS-Architektur (assets/css/style.css)

**Ansatz:** Mobile-first Responsive Design mit CSS Custom Properties

### CSS-Variablen (Theming)
```css
--color-primary, --color-secondary
--color-bg, --color-bg-alt, --color-bg-dark
--color-text, --color-text-light, --color-text-muted
--color-border, --color-success, --color-warning, --color-error
--font-family, --font-size-base, --font-size-heading
--radius-sm / --radius-md / --radius-lg
--shadow-sm / --shadow-md / --shadow-lg
--transition, --container-width (1280px)
```

### Design-Bausteine
- **Layout:** Sticky Header, Container mit max-width, 4-Spalten-Footer
- **Buttons:** `.btn`, `.btn-primary`, `.btn-outline`, `.btn-sm`, `.btn-block`
- **Karten:** `.product-card` mit Hover-Effekten
- **Badges:** `.badge`, `.badge-sale`, `.badge-featured`
- **Farb-Swatches:** `.color-swatch` mit Inline-Hintergrundfarbe
- **Formulare:** Labels, Inputs, Selects, Slider
- **Tabellen:** Responsive Produkttabelle in Settings
- **Modal:** `.modal-overlay` + `.modal` für Bildeditor und 3D-Secure
- **Toast:** Benachrichtigungen mit Slide-In-Animation
- **Badge-Animation:** Puls-Effekt auf Warenkorb-Zähler

### Theming
Farben und Schriften werden dynamisch über `<style>`-Block in `layout.php` aus `settings.json` als CSS-Variablen injiziert. Google Fonts werden per `<link>` eingebunden.

## Layout-Template (templates/layout.php)

Das Master-Template umfasst:
- **Top-Bar:** Versand-/Lieferhinweise
- **Header:** Logo (Text oder Custom-Upload), Suchformular, Warenkorb-Icon mit Badge
- **Navigation:** Kategorien-Links mit aktiver Markierung, Mobile-Menü-Toggle
- **Footer:** 4-Spalten-Grid (Shop-Info, Kundenservice, Rechtliches, Kontakt), Zahlungsarten-Badges (TWINT, VISA, MC)
- **Toast-Container** für Benachrichtigungen
- **Globale JS-Settings:** `window.SHOP_SETTINGS` mit Währung, Versandkosten, MwSt., Gratis-Versand-Schwellenwert

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
