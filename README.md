# Passwort Generator

Ein sicherer und benutzerfreundlicher Passwort-Generator für WordPress mit Stärke-Anzeige und moderner Benutzeroberfläche. Erstellen Sie starke, zufällige Passwörter direkt auf Ihrer Website – ideal für Besucher, die sichere Zugangsdaten benötigen.

**Version:** 2.0  
**Autor:** Sven Gauditz  
**Lizenz:** GPL v2 or later  
**Text Domain:** passwort-generator

---

## Inhaltsverzeichnis

- [Features](#features)
- [Voraussetzungen](#voraussetzungen)
- [Installation](#installation)
- [Verwendung](#verwendung)
- [Optionen im Detail](#optionen-im-detail)
- [Sicherheit](#sicherheit)
- [Übersetzungen](#übersetzungen)
- [Anpassungen](#anpassungen)
- [Technische Details](#technische-details)
- [Changelog](#changelog)
- [Support](#support)

---

## Features

### Passwort-Generierung
- **Kryptographisch sicher:** Verwendet die Web Crypto API (`crypto.getRandomValues()`) für unvorhersehbare Zufallszahlen – keine schwache `Math.random()`-Methode
- **Anpassbare Länge:** Passwortlänge von 8 bis 64 Zeichen per Slider einstellbar (Standard: 16 Zeichen)
- **Flexible Zeichensätze:** Aktivieren oder deaktivieren Sie Großbuchstaben, Kleinbuchstaben, Zahlen und Sonderzeichen
- **Sonderzeichen:** Umfangreicher Zeichensatz: `!@#$%^&*()_+-=[]{}|;:,.<>?`
- **Mindestanzahl Sonderzeichen:** Optional 0 bis 10 Sonderzeichen garantieren (Standard: 2)
- **Mehrdeutige Zeichen vermeiden:** Option, um leicht verwechselbare Zeichen wie 0/O, 1/l/I auszuschließen – nützlich bei handschriftlicher Eingabe oder schlechten Displays

### Benutzerfreundlichkeit
- **Passwort-Stärke-Anzeige:** Live-Anzeige der Passwortstärke (Schwach, Ausreichend, Gut, Stark) mit farbiger Fortschrittsanzeige
- **Ein-/Ausblenden:** Button zum Sichtbarmachen oder Maskieren des generierten Passworts
- **Ein-Klick-Kopieren:** Passwort direkt in die Zwischenablage kopieren – nutzt die moderne Clipboard API mit Fallback für ältere Browser
- **Toast-Benachrichtigungen:** Dezente Rückmeldungen statt störender Alert-Dialoge
- **Auto-Generierung:** Ein neues Passwort wird automatisch beim Laden der Seite erstellt
- **Responsives Design:** Optimiert für Desktop, Tablet und Smartphone

### Technik
- **Barrierefreiheit:** ARIA-Labels, semantische Struktur und Tastaturbedienung
- **Übersetzungsbereit:** Vollständig mit WordPress-i18n vorbereitet
- **Leichtgewichtig:** Nur eine CSS-Datei, kein JavaScript-Framework erforderlich
- **Keine externen Abhängigkeiten:** Läuft komplett lokal im Browser

---

## Voraussetzungen

- WordPress 5.0 oder höher
- PHP 7.4 oder höher
- Moderne Browser mit Unterstützung für `crypto.getRandomValues()` und optional `navigator.clipboard` (alle aktuellen Browser)

---

## Installation

### Über WordPress-Admin
1. Laden Sie das Plugin als ZIP-Datei herunter oder kopieren Sie den Ordner `passwort-generator` in `wp-content/plugins/`
2. Gehen Sie in WordPress zu **Plugins → Installierte Plugins**
3. Aktivieren Sie **Passwort Generator**

### Manuell per FTP
1. Laden Sie den kompletten Ordner `passwort-generator` in `wp-content/plugins/` hoch
2. Aktivieren Sie das Plugin im WordPress-Backend unter **Plugins**

### Ordnerstruktur
```
passwort-generator/
├── passwort-generator.php    # Haupt-Plugin-Datei
├── assets/
│   └── css/
│       └── style.css         # Styles
├── languages/                # Übersetzungsdateien (optional)
└── README.md
```

---

## Verwendung

### Shortcode

Fügen Sie den Shortcode an beliebiger Stelle in eine Seite oder einen Beitrag ein:

```
[passwort_generator]
```

Der Generator wird als interaktive Karte mit allen Optionen angezeigt. Besucher können Passwörter generieren, die Stärke prüfen und das Ergebnis per Klick kopieren.

### Beispiele

- **Eigene Seite:** Erstellen Sie eine Seite „Passwort-Generator“ und fügen Sie `[passwort_generator]` ein
- **Widget:** In vielen Themes können Shortcodes in Text-Widgets verwendet werden
- **Beitrag:** Auch in Blogbeiträgen oder anderen Inhalten einsetzbar

---

## Optionen im Detail

| Option | Beschreibung | Standard |
|--------|--------------|----------|
| **Länge** | Anzahl der Zeichen im Passwort (8–64) | 16 |
| **Großbuchstaben (A-Z)** | 26 Großbuchstaben einbeziehen | Aktiviert |
| **Kleinbuchstaben (a-z)** | 26 Kleinbuchstaben einbeziehen | Aktiviert |
| **Zahlen (0-9)** | Ziffern 0 bis 9 einbeziehen | Aktiviert |
| **Sonderzeichen** | Zeichen wie !@#$%^&*()_+-=[]{}|;:,.<>? | Aktiviert |
| **Mehrdeutige Zeichen vermeiden** | 0, O, 1, l, I ausschließen | Deaktiviert |
| **Min. Sonderzeichen** | Garantierte Mindestanzahl an Sonderzeichen (0–10) | 2 |

### Hinweise
- Mindestens eine Zeichenart muss ausgewählt sein
- Bei sehr kurzer Länge und vielen geforderten Sonderzeichen wird eine Meldung angezeigt – erhöhen Sie die Länge oder reduzieren Sie die Sonderzeichenanzahl
- Die Stärke-Anzeige berücksichtigt Länge, Groß-/Kleinbuchstaben, Zahlen und Sonderzeichen

---

## Sicherheit

- **Keine Server-Generierung:** Passwörter werden ausschließlich im Browser erzeugt – sie verlassen niemals den Rechner des Besuchers
- **Kryptographischer Zufall:** `crypto.getRandomValues()` entspricht aktuellen Sicherheitsstandards (CSPRNG)
- **Keine Speicherung:** Weder Passwörter noch Einstellungen werden in der Datenbank oder in Cookies gespeichert
- **Escaping:** Alle Plugin-Ausgaben sind mit WordPress-Escaping-Funktionen gesichert

---

## Übersetzungen

Das Plugin nutzt die Text Domain `passwort-generator`. Übersetzungsdateien können im Ordner `languages/` abgelegt werden:

```
languages/
├── passwort-generator-de_DE.po
├── passwort-generator-de_DE.mo
└── …
```

Übersetzen Sie die Plugin-Strings mit Tools wie [Poedit](https://poedit.net/) oder [Loco Translate](https://wordpress.org/plugins/loco-translate/).

---

## Anpassungen

### Farben und Design

Die Styles nutzen CSS-Variablen. Über themespezifisches CSS können Sie das Aussehen anpassen:

```css
/* Im Theme oder per Customizer */
#pwg-generator {
    --pwg-bg: #0f172a;
    --pwg-card-bg: #1e293b;
    --pwg-accent: #38bdf8;
    /* weitere Variablen siehe assets/css/style.css */
}
```

### Hellmodus

Für einen hellen Hintergrund können Sie dem `body` die Klasse `pwg-light-mode` zuweisen oder den Container mit `pwg-light-mode` versehen – das Plugin stellt automatisch angepasste Farben bereit.

---

## Technische Details

### Dateien
- `passwort-generator.php` – Registriert Shortcode, lädt Styles, rendert HTML und JavaScript
- `assets/css/style.css` – Alle visuellen Styles (CSS-Variablen, responsive, Dark Mode)

### Hooks
- `init` – Lädt die Textdomain für Übersetzungen

### Shortcode
- **Name:** `passwort_generator`
- **Parameter:** Keine
- **Ausgabe:** HTML-Div mit Generator-UI

### Browser-Unterstützung
- Chrome, Firefox, Safari, Edge (aktuelle Versionen)
- Fallback für `navigator.clipboard`: Nutzung von `document.execCommand('copy')` bei Bedarf

---

## Changelog

### 2.0 (Februar 2025)
- Komplette Überarbeitung des Plugins
- Kryptographisch sichere Zufallsgenerierung (Web Crypto API)
- Passwort-Stärke-Anzeige
- Option „Mehrdeutige Zeichen vermeiden“
- Erweiterter Sonderzeichen-Zeichensatz
- Modernes Dark-Mode-Design
- Toast-Benachrichtigungen statt Alert
- Barrierefreiheit und i18n
- Automatische Generierung beim Laden

### 1.0
- Erste Version
- Grundlegender Passwort-Generator mit Shortcode
- Einstellbare Länge und Zeichensätze

---

## Support

- **Plugin URI:** [https://gauditz.com/](https://gauditz.com/)
- **Autor:** Sven Gauditz

Bei Fragen oder Problemen wenden Sie sich an den Autor oder öffnen Sie ein Issue im Projekt-Repository.

---

## Lizenz

Dieses Plugin ist unter der GPL v2 or later lizenziert.
