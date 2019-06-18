**Dashboard and integration platform for MS**
- 
 
 
 Zum Starten des Projekts:
 - docker-compose up --build
 Beim ersten Start, schlägt die Kompilierung des Auth-Servers fehl.
 Der Grund ist, dass versucht wird auf die Datenbank zuzugreifen.
 Die ist allerdings noch nicht komplett hochgefahren.
 Nachdem die Datenbank hochgefahren ist, Strg + C drücken
 und den Behfel docker-compose up --build nochmals ausführen.

 local website:
 
 - http://localhost:5000 (Auth-Server)
 - http://localhost:4200 (Dashboard)
 - http://localhost:5001 (File-Manager API)
 - http://localhost:4201 (Test Client) 