<?php
// Startar  en sessionshantering för att spåra användarsessioner som är aktiva
session_start();

//  kopplar till connect.php fil som innehåller anslutningsformulär till själva databasen 
include "connect.php";

//Detta är default värden för name och email 
$name = "Guest";
$email = "N/A";
