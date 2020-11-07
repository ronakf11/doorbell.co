# doorbell.co
Social Network for Neighborhoods

PDS Project Readme
Gaurav Agrawal (ga1380) & Ronak Fofaliya (rf1999)
I am using Xampp server 7.3.11 which uses MariaDB (MySql) and Php 7.3.11, so below readme
adheres to the same.

Also, I am using bootstrap (online version) to beautify the look and feel of the pages.
Please extract the file ga1380-rf1999-pdsproject.zip

There would be 1 folder and 4 files present in the folder “ga1380-rf1999-pdsproject”
Schema.sql to create the underlying schema
Insert.sql to enter pre-populated HOODS & BLOCKS.
Script.sql to execute needed procedures and triggers.
README.pdf to explain the code and execution.
pro4 (FOLDER)

css
o style.css
img
o logo.png
o map.png
o profile.png
include
o acceptFriend.php
o addFriend.php
o addNeighbour.php
o addrUpdate.php
o approvalRequest.php
o blockByCity.php
o createThread.php
o footer.php
o form_compose.php
o header.php
o mapMarkers.php
o markAsRead.php
o nav.php
o rejectFriend.php
o signUpMap.php
o threadReply.php
o updateAddGetBlock.php
js
uploads
address.php
approval.php
config.php
confirmation.php
contact.php
friends.php
index.php
login.php
logout.php
messages.php
neighbours.php
profile.php
signup.php
welcome.php
Execution Process:

Place the folder “pro4” under htdocs folder at Xampp installation root folder.
Execute all three SQL files on the server.
After starting the apache and database server (port: 3307), go to “http://localhost/pro4/” in
the browser, index.php is automatically rendered.
