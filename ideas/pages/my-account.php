<?php
require_once "pages/header.php";
if ($userId == "") {
	$html = '<div class="authTitle">
	<h2>MEIN ACCOUNT</h2>
	</div>
	 <div class="authForm">
	 	 <div class="authFormBlock">
	 	 	<h5>ANMELDEN</h5>
	 	 	<p>Benutzername oder E-Mail-Adresse *</p>
	 	 	<input class="authInput"  id="login" type="text">
	 	 	<p>Passwort *</p>
	 	 	<input id="password" type="password" class="authInput">

	 	 	<div class="authButton" id="loginButton">Anmelden</div>
	 	 </div>
	 	 <div class="authFormBlock authFormBlockRight">
	 	 	<h5>NEUES KUNDENKONTO ANLEGEN</h5>
	 	 	<p>E-Mail-Adresse *</p>
	 	 	<input class="authInput"  id="register" type="email">
	 	 	
	 	 	<div class="authButton" id="registerButton">Neues Kundenkonto anlegen</div>
	 	 </div>
	 </div>';
}

else {
	$html ='
<div class="cabinetTitle block">
<div class="innerContent">
	<h2>MEIN ACCOUNT</h2>
	<div class="cabinetSubtitle">dashboard</div>
	</div></div>';
}

echo $html;


?>

	<div class="cabinet block">
		<div class="cabinetArea innerContent">
			
		
	<div class="cabinetSidebar">
		<div class="cabinetSidebarInfo row">
			<div class="cabinetSidebarInfoImg img">
				<img src="img/cabinetImg.png" alt="">
			</div>
			<div class="cabinetSideBarInfoName">SOFAA</div>
			<div class="cabinetSidebarInfoId">#19</div>
		</div>
		<div class="cabinetSidebarMenu">
			<div class="cabinetSidebarMenuItem cabinetSidebarMenuItemActive">DASHBOARD</div>
			<div class="cabinetSidebarMenuItem">BESTELLUNGEN</div>
			<div class="cabinetSidebarMenuItem">ADRESSEN</div>
			<div class="cabinetSidebarMenuItem">ARTIKEL</div>
			<div class="cabinetSidebarMenuItem">ZAHLUNGSMETHODEN</div>
			<div class="cabinetSidebarMenuItem">KONTO-DETAILS</div>
			<div class="cabinetSidebarMenuItem">ABMELDEN</div>
		</div>
	</div>
	<div class="cabinetMain">
		<p>
			Willkommen!
		</p>
		<p>
			Sie haben den FLEXLOOP SIMPLE (LEVEL 3) gekauft. Folgen Sie diesem Link, um den Kurs zu besuchen.
		</p>

		<p>
			In deiner Konto-Übersicht kannst du deine letzten Bestellungen ansehen, deine Liefer- und Rechnungsadresse verwalten und dein Passwort und die Kontodetails bearbeiten.
		</p>

		<div class="dashboardButtons row">
			<div class="dashboardButtonsItem">Bestellungen</div>
			<div class="dashboardButtonsItem">Adressen</div>
			<div class="dashboardButtonsItem">Artikel</div>
			<div class="dashboardButtonsItem">Zahlungsmethoden</div>
			<div class="dashboardButtonsItem">Konto-Details</div>
		</div>
		







	</div>
</div>
</div>



