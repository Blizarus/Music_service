UPDATE audiofiles
SET
    filepath =
REPLACE (
        filepath, 'C:\\WebServers\\home\\music\\www', 'C:\\Games\\xampp\\htdocs\\music'
    ),
    coverpath =
REPLACE (
        coverpath, 'C:\\WebServers\\home\\music\\www', 'C:\\Games\\xampp\\htdocs\\music'
    );

UPDATE genre
SET
    coverpath =
REPLACE (
        coverpath, 'C:\\WebServers\\home\\music\\www', 'C:\\Games\\xampp\\htdocs\\music'
    );

UPDATE artist
SET
    coverpath =
REPLACE (
        coverpath, 'C:\\WebServers\\home\\music\\www', 'C:\\Games\\xampp\\htdocs\\music'
    );