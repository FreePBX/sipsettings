��    �      D  �   l      8  �   9  F   �  !        =     U     ^  	   j     t     �  i   �  !        2     C  t   [  �  �  L   �  �   �  �   v     I  /   _  �  �  \        z     �     �     �  
   �     �     �     �     �       p     �   �  d   "  /   �     �     �     �  6   �          1  <   :     w     �     �  
   �     �     �     �  
   �  
   �  4        6     >  	   O     Y  ~   j     �  o   �  V   m     �     �     �  �   �     �     �  �   �     �     �  '   �     �               $     '     4     :     G     O     a     j     �  	   �  
   �     �     �  �  �     S     i     }     �  �   �  -   �  G   �  %      @   9     z   
   �!     �!     �!     �!     �!     �!     �!     �!     �!  �  �!  �  �#  9   X%     �%     �%     �%     �%  1   �%    &  v  '  H   �(     �(     �(      �(  6   )     J)     O)     U)     X)     m)     �)  -   �)     �)     �)     �)  �  �)  �   �+  l   Y,  *   �,     �,     -     -     )-  &   :-  #   a-  �   �-  &    .     G.     [.  x   w.  q  �.  b   b2  �   �2  �   [3     64  0   Q4  �  �4  m   ~6     �6     �6     7      7  
   07     ;7     L7     b7     {7     �7  ~   �7  �   8  o   �8  L   :9     �9     �9     �9  8   �9     �9     
:  A   :     X:     r:  %   �:     �:     �:     �:     �:     �:     �:  :   ;  	   I;     S;  
   g;     r;  �   �;     <  �   #<  m   �<     *=     /=     G=  �   d=     6>     D>  	  X>     b?     n?  0   ?     �?     �?  #   �?     �?     �?     @     @     $@     -@     F@  !   T@     v@     |@     �@     �@     �@    �@     �B     �B     �B     C  \  -C  /   �D  [   �D  ,   E  S   CE  @  �E  
   �F     �F     �F  	   G     G     $G     8G     EG     ZG    oG    vI  L   zK     �K     �K     �K     �K  9   L  d  ;L  �  �M  T   pO     �O     �O  '   �O  Z   P     rP     wP     {P     �P  #   �P     �P  .   �P  $   �P  "   Q     8Q     q       2                               >   \          t   b       +       	   c   f   9   C       <   T              a   @       K   W      3           z   l       m   �   _             D   �          ~       *   P   �          �              ;      N              u       F   B   E          `       |   G   7      
   p   V              j   d   i   0                  !           5   M   (          L      &       '          %          e       �         s   {   A   O       g   w       ,   H   )   I       6   h   $      ?   4      n   v   "   ]   J   =   Q   k      #   x   .   �   Z   S       y   ^       o   r       R   8   1      X   -   [       /   U   :   Y      }             If you clear each codec and then add them one at a time, submitting with each addition, they will be added in order which will effect the codec priority.  See current version of Asterisk for limitations on SRV functionality. %s must be a non-negative integer %s must be alphanumeric Adaptive Add Address Add Field Add Local Network Field Advanced General Settings After you enable/disable a transport, asterisk needs to be <strong>restarted</strong>, not just reloaded. Allow Anonymous Inbound SIP Calls Allow SIP Guests Allow Transports Reload Allow transports to be reloaded when the PBX is reloaded.  Enabling this is not recommended, and may lead to issues. Allowing Inbound Anonymous SIP calls means that you will allow any call coming in form an un-known IP source to be directed to the 'from-pstn' side of your dialplan. This is where inbound calls come in. Although FreePBX severely restricts access to the internal dialplan, allowing Anonymous SIP calls does introduced additional security risks. If you allow SIP URI dialing to your PBX or use services like ENUM, you will be required to set this to Yes for Inbound traffic to work. This is NOT an Asterisk sip.conf setting, it is used in the dialplan in conjuction with the Default Context. If that context is changed above to something custom this setting may be rendered useless as well as if 'Allow SIP Guests' is set to no. An Error occurred trying fetch network configuration and external IP address An unknown port conflict has been detected in PJSIP. Please check and validate your PJSIP Ports to ensure they're not overlapping Asterisk NAT setting:<br /> yes = Always ignore info and assume NAT<br /> no = Use NAT mode only according to RFC3581 <br /> never = Never attempt NAT mode or RFC3581 <br /> route = Assume NAT, don't send rport Asterisk SIP Settings Asterisk is currently using %s for SIP Traffic. Asterisk: canreinvite. yes: standard reinvites; no: never; nonat: An additional option is to allow media path redirection (reinvite) but only when the peer where the media is being sent is known to not be behind a NAT (as the RTP core can determine it based on the apparent IP address the media arrives from; update: use UPDATE for media path redirection, instead of INVITE. (yes = update + nonat) Asterisk: externrefresh. How often to lookup and refresh the External Host FQDN, in seconds. Audio Codecs CA Chain File CHANSIP TCP Disabled Call Events Candidates Certificate File Certificate Manager Chan PJSIP Settings Chan SIP Chan SIP Settings Chansip was assigned a port that was already in use for TLS traffic. The Chansip TLS port has been changed to %s Chansip was assigned the same port as pjsip for TCP traffic. Chansip has had the tcpenable setting removed, and is no longer listening for TCP connections. Chansip was assigned the same port as pjsip for UDP traffic. The Chansip port has been changed to %s Check to enable and then choose allowed codecs. Codecs Default Default TLS Port Assignment Default length of incoming and outgoing registrations. Detect Network Settings Disabled Don't Require verification of server certificate (TLS ONLY). Don't Verify Server Dynamic Host Dynamic Host can not be blank Dynamic IP ERRORS Edit Settings Enable Jitter Buffer Enable TCP Enable TLS Enable server for incoming TLS (secure) connections. Enabled Enter IP Address Error: %s External Address External FQDN as seen on the WAN side of the router and updated dynamically, e.g. mydomain.example.com. (asterisk: externhost) External IP Address External IP can not be blank when NAT Mode is set to Static and no default IP address provided on the main page External Static IP or FQDN as seen on the WAN side of the router. (asterisk: externip) Fixed Force Jitter Buffer General SIP Settings Hostname or address for the TURN server to be used as a relay. The port number is optional. If omitted the default value of 3478 will be used. This option is blank by default. ICE Blacklist ICE Host Candidates IMPORTANT: Only use this functionality when your Asterisk server is behind a one-to-one NAT and you know what you're doing. If you do define anything here, you almost certainly will NOT want to specify 'stunaddr' or 'turnaddr' above. IP Addresses IP Configuration If blank, will use the default settings Implementation Insecure Jitter Buffer Max Size No RTP Settings Reset SIP Settings Seconds Security Settings Settings Show Advanced Settings Start Static IP Strict RTP Submit Submit Changes Subnets to exclude from ICE host, srflx and relay discovery. This is useful to optimize the ICE process where a system has multiple host address ranges and/or physical interfaces and certain of them are not expected to be used for RTP. For example, VPNs and local interconnections may not be suitable or necessary for ICE. Multiple subnets may be listed. If left unconfigured, all discovered host addresses are used. TLS/SSL/SRTP Settings TURN Server Address TURN Server Password TURN Server Username Terminate call if rtptimeout seconds of no RTP or RTCP activity on the audio channel when we're not on hold. This is to be able to hangup a call in the case of a phone disappearing from the net, like a powerloss or someone tripping over a cable. The port that this transport should listen on This address will be provided to clients if NAT is enabled and detected This is most commonly used for WebRTC This is the default Codec setting for new Trunks and Extensions. This lets you explicitly control the SIP Protocol that listens on the default SIP TLS port (5061). If an option is not available, it is because that protocol is not enabled, or, that protocol does not have TLS enabled. If you change this, you will have to restart Asterisk Transports Unknown Error Unknown Type Use  Verify Client Verify Server Video Codecs Video Support WebRTC Settings When Asterisk is behind a static one-to-one NAT and ICE is in use, ICE will expose the server's internal IP address as one of the host candidates. Although using STUN (see the 'stunaddr' configuration option) will provide a publicly accessible IP, the internal IP will still be sent to the remote peer. To help hide the topology of your internal network, you can override the host candidates that Asterisk will send to the remote peer. When set Asterisk will allow Guest SIP calls and send them to the Default SIP context. Turning this off will keep anonymous SIP calls from entering the system. Doing such will also stop 'Allow Anonymous Inbound SIP Calls' from functioning. Allowing guest calls but rejecting the Anonymous SIP calls below will enable you to see the call attempts and debug incoming calls that may be mis-configured and appearing as guests. Whether to enable or disable UDP checksums on RTP traffic Yes Yes with FEC Yes with Redundancy Yes with no error correction You can change this on the Advanced Settings Page You have Asterisk %s which no longer needs to be restarted for transport changes if 'Allow Transports Reload' is set to 'Yes' above. Note: If 'Allow Transports Reload' is set to 'Yes' reloading after changing transports does have the possibility to drop calls. You may set any other SIP settings not present here that are allowed to be configured in the General section of sip.conf. There will be no error checking against these settings so check them carefully. They should be entered as:<br /> [setting] = [value]<br /> in the boxes below. Click the Add Field box to add additional fields. Blank boxes will be deleted when submitted. You may use this to to define an additional local network per interface. already exists chan_pjsip and chan_sip checking for sipsettings table.. fatal error occurred populating defaults, check module kb/s never no none, creating table populating default codecs.. route rtpholdtimeout must be higher than rtptimeout saving previous value of %s ulaw, alaw, gsm, g726 added yes Project-Id-Version: German (FreePBX)
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2020-06-06 15:00+0000
PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE
Last-Translator: FULL NAME <EMAIL@ADDRESS>
Language-Team: German <http://*/projects/freepbx/sipsettings/de/>
Language: de
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Weblate 3.0.1
  Wenn Sie jeden Codec löschen und sie dann einen nach dem anderen hinzufügen und jedes Mal speichern, werden Sie der Reihe nach hinzugefügt, was die Priorität der Codecs beinflusst.  Prüfen Sie, welche Einschränkungen für die SRV-Funktionalität in der aktuellen Asterisk-Version gelten. %s muss eine nichtnegative ganze Zahl sein %s muss alphanumerisch sein Adaptiv Adresse hinzufügen Feld hinzufügen Feld für lokales Netzwerk hinzufügen Erweiterte allgemeine Einstellungen Nach dem Aktivieren/Deaktivieren eines Transports muss Asterisk <strong>neu gestartet</strong> werden, ein erneutes Laden der Konfiguration genügt nicht. Anonyme eingehende SIP-Anrufe zulassen SIP-Gäste zulassen Erlaube Transport neu laden Erlaube Transporte neu zu laden, wenn PBX neu ladet.  Das zu aktivieren ist nicht empfohlen und kann zu Fehlern führen. Das Zulassen anonymer eingehender SIP-Anrufe bedeutet, dass Sie zulassen, dass alle Anrufe, die von einer unbekannten IP-Quelle eingehen, an die "von-pstn"/"from-pstn" -Seite Ihres Wählplans geleitet werden. Hier kommen eingehende Anrufe ins Spiel. Obwohl FreePBX den Zugriff auf den internen Wählplan stark einschränkt, führt das Zulassen von anonymen SIP-Anrufen zu zusätzlichen Sicherheitsrisiken. Wenn Sie die SIP-URI-Anwahl für Ihre TK-Anlage zulassen oder Dienste wie ENUM verwenden, müssen Sie diese Einstellung auf Ja setzen, damit eingehender Datenverkehr funktioniert. Dies ist KEINE Asterisk-sip.conf-Einstellung, sie wird im Wählplan in Verbindung mit dem Standardkontext verwendet. Wenn dieser Kontext in einen benutzerdefinierten Kontext geändert wird, wird diese Einstellung möglicherweise unbrauchbar und wenn "SIP-Gäste zulassen" auf "Nein" gesetzt ist. Bei dem Versuch, die Netzwerkkonfiguration und die externe IP-Adresse zu laden trat ein Fehler auf Ein unbekannter Port-Konflikt wurde in PJSIP erkannt. Bitte überprüfen Sie Ihre PJSIP-Ports, um sicherzustellen, dass Sie sich nicht überschneiden Asterisk-NAT-Einstellung:<br /> yes = Info immer ignorieren und NAT annehmen<br /> no = NAT-Modus gemäß RFC3581 verwenden<br /> never = NAT-Modus oder RFC3581 nie nutzen<br /> route = NAT annehmen, rport nicht senden Asterisk SIP-Einstellungen Asterisk verwendet momentan %s für SIP-Verkehr. Asterisk: canreinvite. ja: Standardwiedereinladungen; Nein niemals; nonat: Eine zusätzliche Option besteht darin, die Umleitung von Medienpfaden zuzulassen (erneut einzuladen), jedoch nur dann, wenn bekannt ist, dass sich der Peer, an den die Medien gesendet werden, nicht hinter einem NAT befindet (da der RTP-Kern dies anhand der scheinbaren IP-Adresse bestimmen kann, auf der die Medien ankommen from; update: Verwenden Sie UPDATE für die Medienpfadumleitung anstelle von INVITE. (yes = update + nonat) Asterisk: externrefresh. Intervall in Sekunden, in dem externe FQDN überprüft und aktualisiert werden soll. Audio-Codecs CA-Chain-Datei CHANSIP TCP deaktiviert Anrufereignisse Kandidaten Zertifikat-Datei Zertifikatsverwaltung Chan-PJSIP-Einstellungen Chan SIP Chan-SIP-Einstellungen CHANSIP wurde ein Port zugewiesen, der bereits für TLS-Verkehr verwendet wurde. Der CHANSIP-TLS-Port wurde nach %s verschoben CHANSIP wurde derselbe Port für TCP-Verkehr zugewiesen wie PJSIP. Die Einstellung 'tcpenable' wurde für CHANSIP entfernt, daher nimmt CHANSIP keine TCP-Verbindungen mehr an. CHANSIP wurde derselbe Port für UDP-Verkehr zugewiesen wie PJSIP. Der Port für CHANSIP wurde auf %s geändert Zum Aktivieren markieren, wählen Sie anschließend die zugelassenen Codecs. Codecs Standard Standard-TLS-Portzuweisung Standardlänge für ein- und ausgehende Registrierungen. Netzwerkeinstellungen erkennen Deaktiviert Verifizierung des Serverzertifikats nicht voraussetzen (nur TLS). Server nicht verifizieren Dynamischer Host Dynamischer Host kann nicht leer sein Dynamische IP FEHLER Einstellungen bearbeiten Jitter-Puffer aktivieren TCP aktivieren TLS aktivieren Eingehende (sichere) TLS-Verbindungen zum Server zulassen. Aktiviert IP-Adresse eingeben Fehler: %s Externe Adresse Externer FQDN wie er auf der WAN-Seite des Routers gesehen und dynamisch aktualisiert wird, z.B. mydomain.example.com. (asterisk: externhost) Externe IP-Adresse Die externe IP kann nicht leer sein, wenn der NAT-Modus auf 'statisch' eingestellt ist und auf der Hauptseite keine Standard-IP-Adresse eingegeben wurde Externe statische IP (oder der FQDN) wie sie auf der WAN-Seite des Routers gesehen wird. (asterisk: externip) Fest Jitter-Puffer erzwingen Allgemeine SIP-Einstellungen Hostname oder Adresse des TURN-Servers, der als Relais genutzt werden soll. Die Portnummer ist optional, falls sie weggelassen wird, wird der Standardport 3478 verwendet. Diese Option ist standardmäßig leer. ICE-Blacklist ICE-Host-Kandidaten WICHTIG: Verwenden Sie diese Funktionalität nur, wenn Ihr Asterisk-Server sich hinter einem Eins-zu-eins-NAT befindet und Sie wissen, was Sie tun. Wenn Sie hier etwas festlegen, werden Sie oben höchstwahrscheinlich KEINE 'stunaddr' oder 'turnaddr' angeben wollen. IP-Adressen IP-Konfiguration Verwendet die Standardeinstellungen, sofern leer Implementierung Unsicher Maximale Größe des Jitter-Puffers Nein RTP-Einstellungen Zurücksetzen SIP-Einstellungen Sekunden Sicherheitseinstellungen Einstellungen Erweiterte Einstellungen anzeigen Start Statische IP Striktes RTP Absenden Änderungen bestätigen Subnetze, die von der ICE-Host-, SRFLX- und Relay-Erkennung ausgeschlossen werden sollen. Dies ist nützlich, um den ICE-Prozess zu optimieren, wenn ein System über mehrere Hostadressbereiche und / oder physische Schnittstellen verfügt und von einigen nicht erwartet wird, dass sie für RTP verwendet werden. Beispielsweise sind VPNs und lokale Verbindungen möglicherweise nicht für ICE geeignet oder erforderlich. Es können mehrere Subnetze aufgelistet sein. Wenn nicht konfiguriert, werden alle erkannten Hostadressen verwendet. TLS-/SSL-SRTP-Einstellungen TURN-Server-Adresse TURN-Server-Passwort TURN-Server-Benutzername Beenden Sie den Anruf, wenn keine RTP- oder RTCP-Aktivität(rtptimeout seconds) auf dem Audiokanal vorhanden ist und das Zeitlimit überschritten wurde, wenn wir nicht in der Warteschleife sind. Dies ist in der Lage, einen Anruf zu beenden, wenn ein Telefon aus dem Netz verschwindet, wie ein Stromausfall oder jemand, der über ein Kabel stolpert. Der Port, den dieser Transport überwachen soll Diese Adresse wird den Clients zur Verfügung gestellt, wenn NAT aktiviert und erkannt wird Dies wird in der Regel für WebRTC verwendet Dies ist die Standard-Codec-Einstellungen für neue Amtsleitungen und Nebenstellen. Auf diese Weise können Sie das SIP-Protokoll, das den Standard-SIP-TLS-Port (5061) überwacht, explizit steuern. Wenn eine Option nicht verfügbar ist, liegt dies daran, dass dieses Protokoll nicht aktiviert ist oder TLS für dieses Protokoll nicht aktiviert ist. Wenn Sie dies ändern, müssen Sie Asterisk neu starten Transporte Unbekannter Fehler Unbekannter Typ Verwende  Client verifizieren Server verifizieren Video-Codecs Video-Unterstützung WebRTC-Einstellungen Wenn sich Asterisk hinter einem statischen Eins-zu-Eins-NAT befindet und ICE verwendet wird, macht ICE die interne IP-Adresse des Servers als einen der Hostkandidaten verfügbar. Obwohl die Verwendung von STUN (siehe die Konfigurationsoption 'stunaddr') eine öffentlich zugängliche IP-Adresse bereitstellt, wird die interne IP-Adresse weiterhin an den Remote-Peer gesendet. Um die Topologie Ihres internen Netzwerks auszublenden, können Sie die Hostkandidaten überschreiben, die Asterisk an den Remote-Peer sendet. Wenn festgelegt, lässt Asterisk Gast-SIP-Anrufe zu und sendet sie an den Standard-SIP-Kontext. Durch Deaktivieren dieser Option wird verhindert, dass anonyme SIP-Anrufe in das System gelangen. Dies führt auch dazu, dass "Anonyme eingehende SIP-Anrufe zulassen" nicht mehr funktioniert. Wenn Sie Gastanrufe zulassen, aber die folgenden anonymen SIP-Anrufe ablehnen, können Sie die Anrufversuche anzeigen und eingehende Anrufe debuggen, die möglicherweise falsch konfiguriert sind und als Gäste angezeigt werden. Ob UDP-Prüfsummen für RTP-Verkehr aktiviert oder deaktiviert werden sollen Ja Ja, mit FEC Ja, mit Redundanz Ja, ohne Fehlerkorrektur Sie können dies in den erweiterten Einstellungen ändern Sie verwenden Asterisk %s, ein Neustart bei Transport-Änderungen ist nicht länger erforderlich, wenn „Neuladen von Transporten zulassen“ oben auf „Ja“ gestellt ist. Hinweis: Wenn „Neuladen von Transporten zulassen“ oben auf „Ja“ gestellt ist, kann ein Neuladen der Konfiguration nach Transport-Änderungen zu Gesprächsabbrüchen führen. Sie können alle anderen hier nicht vorhandenen SIP-Einstellungen vornehmen, die im Bereich Allgemein der sip.conf konfiguriert werden dürfen. Bei diesen Einstellungen wird kein Fehler festgestellt. Überprüfen Sie sie daher sorgfältig. Sie sollten wie folgt eingegeben werden: <br/> [Einstellung] = [Wert] <br/> in den Feldern unten. Klicken Sie auf das Feld Feld hinzufügen, um zusätzliche Felder hinzuzufügen. Leere Felder werden beim Absenden gelöscht. Hiermit können Sie ein zusätzliches lokales Netzwerk pro Schnittstelle definieren. bereits vorhanden chan_pjsip und chan_sip prüfe auf <i>sipsettings</i>-Tabelle.. ein schwerwiegender Fehler trat beim Erzeugen der Standardwerte auf, prüfen Sie das Modul kb/s nie nein keine, erzeuge Tabelle Standardcodecs werden eingespielt.. Route rtpholdtimeout muss höher als rtptimeout sein speichere den vorherigen Wert von %s ulaw, alaw, gsm, g726 hinzugefügt ja 