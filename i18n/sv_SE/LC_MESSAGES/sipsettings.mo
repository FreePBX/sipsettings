��    K      t  e   �      `  �   a  !   �          6  	   ?     I     a     {  �   �     _  �   u  �  *	  \   �
          6     C     R  .   _  	   �     �  /   �     �  g   �  n   C     �     �     �     �  
   �                 V        u     {  H   �  @   �          *     9     O     f  F   u  &   �     �     �     	  '        >     B     O     R     d     w     �     �  	   �     �     �  D   �  	        &     5     F     S     a  v  e     �  6   �     "     '     *  -   F     t  y  x  �   �  "   �  '   �     �     �       #        A  �   U     ;  �   W  �  ,  ^   �       
   6     A  
   Z  /   e     �     �  8   �     �  }   �  ~   m     �                      ;     N     R  	   j  Z   t     �     �  Q   �  L   ;     �     �     �      �     �  V   �  !   C     e     �     �  ,   �     �     �     �     �                 (      7      P      a      }   X   �      �      �      !     !!     -!     :!  �  =!     �"  J   �"     /#     4#     8#  0   R#     �#     I   8   5          6   <      B   &      D            E   9       J   /   K   ?   (                  -         G       0         #   %               "   	   @          4                   >      H   $         .      1                          7      *       )   A   3   C   ,       +                        
   2   F                       ;   =      !   :             '     If you clear each codec and then add them one at a time, submitting with each addition, they will be added in order which will effect the codec priority. %s must be a non-negative integer %s must be alphanumeric Adaptive Add Field Add Local Network Field Advanced General Settings Allow SIP Guests Asterisk NAT setting:<br /> yes = Always ignore info and assume NAT<br /> no = Use NAT mode only according to RFC3581 <br /> never = Never attempt NAT mode or RFC3581 <br /> route = Assume NAT, don't send rport Asterisk SIP Settings Asterisk: bindaddr. The IP address to bind to and listen for calls on the Bind Port. If set to 0.0.0.0 Asterisk will listen on all addresses. It is recommended to leave this blank. Asterisk: canreinvite. yes: standard reinvites; no: never; nonat: An additional option is to allow media path redirection (reinvite) but only when the peer where the media is being sent is known to not be behind a NAT (as the RTP core can determine it based on the apparent IP address the media arrives from; update: use UPDATE for media path redirection, instead of INVITE. (yes = update + nonat) Asterisk: externrefresh. How often to lookup and refresh the External Host FQDN, in seconds. Asterisk: g726nonstandard. If the peer negotiates G726-32 audio, use AAL2 packing order instead of RFC3551 packing order (this is required for Sipura and Grandstream ATAs, among others). This is contrary to the RFC3551 specification, the peer _should_ be negotiating AAL2-G726-32 instead. Audio Codecs Auto Configure Bind Address Bind Address (bindaddr) must be an IP address. Bind Port Call Events Check to enable and then choose allowed codecs. Codecs Control whether subscriptions INUSE get sent ONHOLD when call is placed on hold. Useful when using BLF. Control whether subscriptions already INUSE get sent RINGING when another call is sent. Useful when using BLF. Default Context Disabled Dynamic Host Dynamic Host can not be blank Dynamic IP ERRORS Edit Settings Enabled External Static IP or FQDN as seen on the WAN side of the router. (asterisk: externip) Fixed Force Jitter Buffer Frequency in seconds to check if MWI state has changed and inform peers. Generate manager events when sip ua performs events (e.g. hold). IP Configuration Implementation Jitter Buffer Logging Jitter Buffer Settings Local Networks Localnet netmask must be formatted properly (e.g. 255.255.255.0 or 24) Localnet setting must be an IP address MEDIA & RTP Settings MWI Polling Freq Max Bit Rate Maximum bitrate for video calls in kb/s NAT NAT Settings No Non-Standard g726 Notification & MWI Notify Hold Notify Ringing Other SIP Settings Public IP Registration Settings Reinvite Behavior Settings in %s may override these. Those settings should be removed. Static IP Submit Changes T38 Pass-Through Video Codecs Video Support Yes You may set any other SIP settings not present here that are allowed to be configured in the General section of sip.conf. There will be no error checking against these settings so check them carefully. They should be entered as:<br /> [setting] = [value]<br /> in the boxes below. Click the Add Field box to add additional fields. Blank boxes will be deleted when submitted. already exists fatal error occurred populating defaults, check module kb/s no populating default codecs.. rtpholdtimeout must be higher than rtptimeout yes Project-Id-Version: FreePBX sipsettings
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2018-12-31 14:37-0500
PO-Revision-Date: 2011-03-20 00:00+0100
Last-Translator: Mikael Carlsson <mickecamino@gmail.com>
Language-Team: Swedish
Language: 
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Swedish
X-Poedit-Country: SWEDEN
  Om du avmarkerar varje codec och sedan lägger till och spara dom en åt gången, kommer dom att sorteras i den ordning de läggs till och påverka prioriteten för codec %s måste vara ett positivt heltal %s måste vara ett alfanumeriskt värde Adaptiv Lägg till fält Lägg till lokalt nätverk Avancerade generella inställningar Tillåt SIP-gäster Asterisk NAT-inställningar:<br /> yes = Ignorera alltid info och förutsätt NAT<br /> no = Använd NAT-läge enligt RFC3581 <br /> never = Använd aldrig NAT-läge eller RFC3581 <br /> route = Förutsätt NAT, sänd inte rport  Asterisk SIP-inställningar Asterisk: bindaddr. IP-adressen att binda till och lyssna efter samtal på Bindporten. Om detta sätts till 0.0.0.0 kommer Asterisk att lyssna på alla adresser. Det är rekommenderat att lämna detta fält tomt. Asterisk: canreinvite. ja: standard reinvites; nej: aldrig; nonat: Ett extra val för att tillåta omstyrning av mediaströmmen (reinvite) men endast när peer där strömmen skickas till är känd att inte vara bakom NAT (eftersom RTP kan bestämma det baserat på den synbara IP-adressen strömmen kommer från; update: använd UPDATE för mediaomstyrning i stället för INVITE. (yes = update + nonat) Asterisk: externrefresh. Hur ofta uppslag och uppdatering ska ske för extern FQDN i sekunder. Asterisk: g726nonstandard. Om peer förhandlar G726-32 ljud, använd AAL2 packningsföljd i stället för RFC3551 (detta krävs bland annat för Sipura och Grandstream ATAs). Detta är i motsats till RFC3551 specifikationen där peer _borde_ förhandla AAL2-G726-32 i stället. Ljud-codec Automatisk konfiguration Bindadress Bindadress (bindaddr) måste vara en IP-adress. Bindport Samtalshändelser Markera för att aktivera, välj sedan tillåtna codecs. Codec Används för prenumerationer som är INUSE får skickat till sig ONHOLD när ett samtal är på vänt. Användbart för BLF. Används för prenumerationer som är INUSE får skickat till sig RINGING när ett annat samtal skickas. Användbart för BLF. Standard sammanhang Avaktiverad Dynamisk host Dynamisk host kan inte vara tomt Dynamisk IP-adress FEL Redigera inställningar Aktiverad Extern statisk IP-adress eller FQDN som är på WAN-sidan av routern. (asterisk: externip) Fast Forcera jitterbuffer Antal sekunder mellan kontrollerna om MWI har ändrat läge, meddela sedan peers. Genererar händelser när en sip ua utför händelser, t.ex. lägg på vänt IP-konfiguration Implementation Logga jitterbuffer Inställningar för Jitterbuffer Lokalt nätverk Nätmasken för localnet måste vara korrekt formaterat (t.ex. 255.255.255.0 eller 24) Localnet måste vara en IP-adress Inställningar för MEDIA & RTP Kontrollfrekvens för MWI Max bithastighet Maximal bithastighet i kb/s för videosamtal NAT NAT-inställningar Nej Icke-standard g726 Meddelande & MWI Notify hold Notify ringing Andra SIP-inställningar Publik IP-adress Registreringsinställningar Beteende för reinvite Inställningar i %s kan åsidosätta inställningarna du gör här. Du bör ta bort dom. Statisk IP-adress Spara ändringar T38 Pass-Through Videocodecs Videosupport Ja Du kan göra fler SIP-inställningar som inte visas här men som är tillåtna att konfigurera i den generella sektionen av sip.conf. Ingen felkontroll kommer att göras mot dessa inställningar så kontrollera dom noga. Syntaxen för värdena är <br /> [inställning] = [värde]<br />  i fälten nedan. Klicka på Lägg till fält för att lägga till fler. Tomma fält kommer att tas bort när sidan sparas. finns redan allvarligt fel inträffade när standardvärde skrevs, kontrollera modulen kb/s nej skriver standard codecs.. rtpholdtimeout måste vara högre än rtptimeout ja 