# Directory Traversal

Directory Traversal, ist eine Methode, bei der Angreifer versuchen, auf Dateien oder Verzeichnisse zuzugreifen, auf die sie normalerweise keinen Zugriff haben, indem sie den Dateipfad in einer URL manipulieren. Dies kann zu Offenlegung sensibler Informationen oder zur Ausführung von Befehlen auf dem Webserver führen. Die Bedrohung gilt als Broken Access Control, da die Anwendung nicht überprüft, ob der Benutzer auf die angeforderte Datei zugreifen darf. Somit ist sie eine der häufigsten Sicherheitslücken in Webanwendungen. In diesem Handout wird erläutern, was eine Directory Traversal Attacke ist, wie sie funktioniert, welche Gefahren sie birgt und wie man sich davor schützen kann.

## Beschreibung der Bedrohung

Als Beispiel haben wir hier einen Endpunkt, welcher ein Bild anhand des Dateinamens aus dem Dateisystem lädt und den Inhalt zurückgibt. Der Dateiname wird als Parameter übergeben. Der Endpunkt ist ungeschützt und es wird nicht überprüft, ob der Benutzer auf das Bild zugreifen darf. Die erwartete Verwendung ist, dass der Benutzer den Dateinamen des Bildes angibt, welches er sehen möchte:

```html
<img src="/fileloader.php?file=katze.png" />
```

Der Server lädt dann das Bild aus dem Bilderordner und gibt es zurück. Der Pfad zum Bilderordner ist `/var/www/html/images/`. Der Dateiname wird an den Pfad angehängt und das Bild wird geladen. Mit anderen Worten, der Endpunkt lädt das Bild `/var/www/html/images/katze.png` und gibt es zurück. Dies könnte in PHP wie folgt aussehen:

```php
const BASE_PATH = '/var/www/html/images';

$filename = $_GET['file'];

echo file_get_contents(BASE_PATH . $filename);
```

Ein Angreifer könnte nun versuchen, auf Dateien zuzugreifen, auf die er keinen Zugriff haben sollte, wie zum Beispiel Passwörter. Dazu könnte er versuchen, den Dateinamen zu manipulieren. Wenn der Angreifer beispielsweise versucht, auf die Datei `/etc/passwd` zuzugreifen, könnte er den Dateinamen wie folgt manipulieren:

```html
<img src="/fileloader.php?file=../../../../etc/passwd" />
```

Dabei wird der Dateiname `../../../../etc/passwd` angegeben. Der Server lädt dann die Datei `/var/www/html/images/../../../../etc/passwd` und gibt sie zurück. Mithilfe von `..` wird ein Verzeichnis zurückgegangen, bis man sich im Stammverzeichnis befindet. Danach kann der Angreifer den Pfad zu der Datei angeben, auf die er zugreifen möchte. Der reale Pfad, der geladen wird, ist `/etc/passwd`. Da der Server nicht überprüft, ob der Benutzer auf die Datei zugreifen darf, wird die Datei geladen und zurückgegeben.

## Schutzmassnahmen

Die effektivste Methode zur Vermeidung von Sicherheitslücken besteht darin, die Übergabe von Benutzereingaben an Dateisystem-APIs gänzlich zu vermeiden. Viele Anwendungsfunktionen, die dies tun, können umgeschrieben werden, um das gleiche Verhalten auf sicherere Weise zu erreichen. So könnte aus dem Beispiel oben ein Endpunkt erstellt werden, welcher das Bild anhand einer ID lädt. Die ID wird dann in der Datenbank gespeichert und der Dateiname wird nicht mehr an den Endpunkt übergeben. Der Endpunkt könnte dann wie folgt aussehen:

```html
<img src="/fileloader.php?id=1" />
```

Der Endpunkt lädt dann das Bild mit der ID `1` aus der Datenbank und gibt es zurück. Der Pfad zum Bilderordner wird nicht mehr an den Endpunkt übergeben. Dies könnte vereinfacht in PHP wie folgt aussehen:

```php
const BASE_PATH = '/var/www/html/images';

$id = $_GET['id'];

// Datenbankabfrage um Dateinamen zu erhalten anhand der ID
$filename = getFilenameFromDatabase($id);

echo file_get_contents(BASE_PATH . $filename);
```

Wenn es nicht möglich ist, die Übergabe von Benutzereingaben an Dateisystem-APIs zu vermeiden, sollte die Eingabe validiert werden. Dabei wird überprüft, ob der absolute Pfad dem erwarteten Pfad entspricht. Dies kann in PHP mit der Funktion `realpath()` gemacht werden. Diese Funktion gibt den absoluten Pfad zurück und löst dabei alle symbolischen Links auf. Zudem sollte der Zugriff nur auf Dateien mit der erwarteten Dateiendung erlaubt werden. Dies könnte in PHP wie folgt aussehen:

```php
const BASE_PATH = '/var/www/html/images';

$filename = $_GET['file'];

$realPath = realpath(BASE_PATH . $filename);
$basePath = basename($realPath);
if ($basePath !== BASE_PATH) {
    die('Invalid filename');
}

if (!str_ends_with($realPath, '.png')) {
    die('Invalid filename');
}

echo file_get_contents($realPath);
```

Diese Implementierung überprüft, ob der absolute Pfad dem erwarteten Pfad entspricht. Wenn dies nicht der Fall ist, wird die Anfrage abgebrochen. Zudem wird überprüft, ob die Dateiendung `.png` ist. Wenn dies nicht der Fall ist, wird die Anfrage abgebrochen. Wenn die Eingabe validiert wurde, kann der absolute Pfad verwendet werden, um die Datei zu laden.

### Anmerkung

Das Überprüfen auf `..` oder `/` ist nicht ausreichend, da der Angreifer andere Zeichen verwenden könnte, um auf Dateien zuzugreifen. Zum Beispiel könnte er diese Zeichen über URL-Encoding umgehen. Dies würde dann wie folgt aussehen:

```html
<img src="/fileloader.php?file=%2e%2e%2fetc/passwd" />
```

Was dem folgenden entspricht:

```html
<img src="/fileloader.php?file=../etc/passwd" />
```

Aus diesem Grund sollte die Eingabe mit `realpath()` validiert werden, da die Funktion den Pfad zurückgibt, welcher auch wirklich geladen wird.

## Unterschied zu Path Traversal

Directory Traversal und Path Traversal sind zwei Begriffe, die oft synonym verwendet werden. Es gibt jedoch einen kleinen Unterschied zwischen den beiden. Bei Directory Traversal wird versucht, auf Dateien oder Verzeichnisse zuzugreifen, auf die der Benutzer keinen Zugriff haben sollte. Bei Path Traversal wird versucht, auf Dateien oder Verzeichnisse zuzugreifen, die sich nicht im aktuellen Verzeichnis befinden.
