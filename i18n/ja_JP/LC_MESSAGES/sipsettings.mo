Þ    S      ´  q   L             !   ¬     Î     æ  	   ï     ù       !   +     M  Ö  ^  L   5  Ò        U  ´   k       \   ®          ,     9     H  .   U  	          /        Ê  g   Ñ  n   9     ¨     ¸     Á     Î  
   ì     ÷     þ            V   %     |       H     @   ß           1     @     V     m  F   |  &   Ã     ê     ÿ       '     0   E     v     z                    ¯     »     Ê  	   Ý     ç     ÷            D   (  	   m     w               ¤     ²  v  ¶     -  6   <     s     x     {  -        Å     á  ³  å  »     6   U  ?        Ì     Õ  6   î     %  3   ;     o  æ    e   l  ù   Ò     Ì   D  à   À  %"  d   æ#    K$     Ø%     î%     û%  S   &     j&     &  o   &     '     '  «   µ'  !   a(     (     (  E   ¦(     ì(  	   )     )     )     ")  j   7)     ¢)  !   ¨)  O   Ê)     *     ¢*     «*  !   ²*  !   Ô*     ö*     +  n   +     ,     &,     ?,  4   X,  >   ,     Ì,  	   Ð,  	   Ú,     ä,     ò,     ÿ,     -     -     5-     G-     Z-     m-     -  x   -      .     	.     .     ,.     E.     [.  Û  b.     >0  u   W0     Í0  	   Ò0  -   Ü0  Z   
1     e1     1         %   '          -   !   ;       $   S   M                @      H   5          =          >       G          2          O   0           :       3              K         ,       1   +   /   &   (       8   .         9       B   4   )   D   I      E   
                 L   "                     7   P   ?   R   C         Q   A   F   J                                   	   N           <             #   6   *            If you clear each codec and then add them one at a time, submitting with each addition, they will be added in order which will effect the codec priority. %s must be a non-negative integer %s must be alphanumeric Adaptive Add Field Add Local Network Field Advanced General Settings Allow Anonymous Inbound SIP Calls Allow SIP Guests Allowing Inbound Anonymous SIP calls means that you will allow any call coming in form an un-known IP source to be directed to the 'from-pstn' side of your dialplan. This is where inbound calls come in. Although FreePBX severely restricts access to the internal dialplan, allowing Anonymous SIP calls does introduced additional security risks. If you allow SIP URI dialing to your PBX or use services like ENUM, you will be required to set this to Yes for Inbound traffic to work. This is NOT an Asterisk sip.conf setting, it is used in the dialplan in conjuction with the Default Context. If that context is changed above to something custom this setting may be rendered useless as well as if 'Allow SIP Guests' is set to no. An Error occurred trying fetch network configuration and external IP address Asterisk NAT setting:<br /> yes = Always ignore info and assume NAT<br /> no = Use NAT mode only according to RFC3581 <br /> never = Never attempt NAT mode or RFC3581 <br /> route = Assume NAT, don't send rport Asterisk SIP Settings Asterisk: bindaddr. The IP address to bind to and listen for calls on the Bind Port. If set to 0.0.0.0 Asterisk will listen on all addresses. It is recommended to leave this blank. Asterisk: canreinvite. yes: standard reinvites; no: never; nonat: An additional option is to allow media path redirection (reinvite) but only when the peer where the media is being sent is known to not be behind a NAT (as the RTP core can determine it based on the apparent IP address the media arrives from; update: use UPDATE for media path redirection, instead of INVITE. (yes = update + nonat) Asterisk: externrefresh. How often to lookup and refresh the External Host FQDN, in seconds. Asterisk: g726nonstandard. If the peer negotiates G726-32 audio, use AAL2 packing order instead of RFC3551 packing order (this is required for Sipura and Grandstream ATAs, among others). This is contrary to the RFC3551 specification, the peer _should_ be negotiating AAL2-G726-32 instead. Audio Codecs Auto Configure Bind Address Bind Address (bindaddr) must be an IP address. Bind Port Call Events Check to enable and then choose allowed codecs. Codecs Control whether subscriptions INUSE get sent ONHOLD when call is placed on hold. Useful when using BLF. Control whether subscriptions already INUSE get sent RINGING when another call is sent. Useful when using BLF. Default Context Disabled Dynamic Host Dynamic Host can not be blank Dynamic IP ERRORS Edit Settings Enabled External Address External Static IP or FQDN as seen on the WAN side of the router. (asterisk: externip) Fixed Force Jitter Buffer Frequency in seconds to check if MWI state has changed and inform peers. Generate manager events when sip ua performs events (e.g. hold). IP Configuration Implementation Jitter Buffer Logging Jitter Buffer Settings Local Networks Localnet netmask must be formatted properly (e.g. 255.255.255.0 or 24) Localnet setting must be an IP address MEDIA & RTP Settings MWI Polling Freq Max Bit Rate Maximum bitrate for video calls in kb/s Migrate rtp.conf values if needed and initialize NAT NAT Settings No Non-Standard g726 Notification & MWI Notify Hold Notify Ringing Other SIP Settings Public IP RTP Port Ranges Registration Settings Reinvite Behavior Settings Settings in %s may override these. Those settings should be removed. Static IP Submit Changes T38 Pass-Through Video Codecs Video Support Yes You may set any other SIP settings not present here that are allowed to be configured in the General section of sip.conf. There will be no error checking against these settings so check them carefully. They should be entered as:<br /> [setting] = [value]<br /> in the boxes below. Click the Add Field box to add additional fields. Blank boxes will be deleted when submitted. already exists fatal error occurred populating defaults, check module kb/s no populating default codecs.. rtpholdtimeout must be higher than rtptimeout saving previous value of %s yes Project-Id-Version: FreePBX
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2019-12-04 11:30+0530
PO-Revision-Date: 2017-05-23 13:16+0200
Last-Translator: Kenichi <k.fukaumi@qloog.com>
Language-Team: Japanese <http://weblate.freepbx.org/projects/freepbx/sipsettings/ja_JP/>
Language: ja_JP
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Weblate 2.4
  ãã¹ã¦ã®ã³ã¼ããã¯ãå¤ãããã®å¾ä¸ã¤ãã¤è¿½å ããæ¯ã«å¤æ´ãé©ç¨ããã¨ãè¿½å ããé çªã§ã³ã¼ããã¯ãã©ã¤ãªãªãã£ãè¨­å®ããã¾ãã %s ã¯éè² ã®æ´æ°ã§ãªããã°ãªãã¾ããã %s ã«ã¯æ°å­ã¨ã¢ã«ãã¡ãããããä½¿ãã¾ããã Adaptive ãã£ã¼ã«ããè¿½å  ã­ã¼ã«ã«ãããã¯ã¼ã¯ãã£ã¼ã«ããè¿½å  é«åº¦ãªä¸è¬è¨­å® å¿åã®ã¤ã³ãã¦ã³ãSIPçä¿¡ãè¨±å¯ãã SIPã²ã¹ããè¨±å¯ ãå¿åã®ã¤ã³ãã¦ã³ãSIPçä¿¡ãè¨±å¯ãããã¨ã¯ãæªç¥ã®IPããã®çä¿¡ãããã¤ã¤ã«ãã©ã³ã®from-pstnå´ã¸åããæ¥ç¶ãè¨±å¯ãããã¨ããæå³ã§ããã¤ã³ãã¦ã³ãçä¿¡ã¯ããããå¥ãã¾ããFreePBXã¯ãåé¨ã®ãã¤ã¤ã«ãã©ã³ã¸ã®ã¢ã¯ã»ã¹ãå³ããå¶éãã¦ãã¾ãããå¿åã®SIPçä¿¡ãè¨±å¯ããäºã«ãããã»ã­ã¥ãªãã£ä¸ã®ãªã¹ã¯ãå¢ããå¯è½æ§ãããã¾ããPBXã§ã¯SIP URIãã¤ã¤ã«ããENUMãªã©ã®ãµã¼ãã¹å©ç¨ããå ´åãçä¿¡ãåããããã«ã¯ãããYesã«è¨­å®ããå¿è¦ãããã¾ããããã¯Asteriskã®sip.confè¨­å®ã§ã¯ãªãããã¤ã¤ã«ãã©ã³ã§ããã©ã«ãã³ã³ãã­ã¹ãã¨åããã¦ä½¿ç¨ãããè¨­å®ã§ããåè¿°ã®æ§ã«ã³ã³ãã­ã¹ããä½ãããã«ã¹ã¿ã è¨­å®ã«ãªã£ã¦ããã¨ããSIPã²ã¹ããè¨±å¯ããããnoã«è¨­å®ãããã¨åæ§ã«ããã®è¨­å®ã¯ç¡å¹ã«ãªãå¯è½æ§ãããã¾ãã ãããã¯ã¼ã¯è¨­å®åã³å¤é¨IPã¢ãã¬ã¹ãååºãä¸­ã«ã¨ã©ã¼ãçºçãã¾ããã Asterisk NAT è¨­å®: <br /> yes = å¸¸ã«infoãç¡è¦ãã¦ãNATã¨ãã¦æ±ã <br/> no = RFC3581ã«å¾ã£ã¦ãNATã¢ã¼ããä½¿ç¨ <br/> never = RFC3581ã¨NATã¢ã¼ããä½¿ããªã <br/> route = NATãåæã¨ãã¦ãrportãéä¿¡ããªã Asterisk SIP è¨­å® å¯¾å¿ããAsteriskè¨­å®ï¼bindaddrããã®IPã¢ãã¬ã¹ã®ããã¤ã³ããã¼ãã§æå®ãããã¼ãçªå·ãlistenï¼åä¿¡å¾ã¡ï¼ãã¾ãã0.0.0.0ã«è¨­å®ããå ´åã«ã¯ããã®ãã¹ãã«å²ãå½ã¦ããããã¹ã¦ã®IPã¢ãã¬ã¹ã§listenãã¾ããç©ºã«è¨­å®ãããã¨ãæ¨å¥¨ãã¾ãã å¯¾å¿ããAsteriskè¨­å®: canreinvideãyesï¼æ¨æºã®reinviteãnoï¼reinviteããªããnonatï¼éä¿¡åãã¢ããNATããã¦ããªãå ´åãã¡ãã£ã¢ãã¹ãªãã¤ã¬ã¯ãï¼reinviteï¼ãè¨±å¯ãã¾ãï¼RTP ã³ã¢ã¯ãã¡ãã£ã¢éä¿¡åã®è¦ããã®IPã¢ãã¬ã¹ããã¼ã¹ã«å¤æ­ãã¾ã)ãupdateï¼ã¡ãã£ã¢ãã¹ãªãã¤ã¬ã¯ãã«ãINVITEã®ä»£ããã«UPDATEãä½¿ç¨ãã¾ããï¼yes = update + nonatï¼ å¯¾å¿ããAsteriskè¨­å®ï¼externrefreshãå¤é¨ãã¹ãFQDNã®åç§ã¨æ´æ°ééï¼ç§ï¼ã å¯¾å¿ããAsteriskè¨­å®ï¼g726nonstandardããã¢ãG726-32é³å£°ããã´ã·ã¨ã¼ã·ã§ã³ããå ´åãRFC3551ããã­ã³ã°ãªã¼ãã¼ã®ä»£ããã«AAL2ããã­ã³ã°ãªã¼ãã¼ãä½¿ãã¾ã (Sipuraã¨Grandstream ATAãªã©ãä½¿ãå ´åã«ã¯å¿è¦)ãRFC3551ä½¿ç¨æã¨ã¯éã«ãªãã¾ãããã¢ã¯ãAAL2-G726-32ããã´ã·ã¨ã¼ã·ã§ã³ããªããã°ãªãã¾ããã é³å£°ã³ã¼ããã¯ èªåè¨­å® ãã¤ã³ãIPã¢ãã¬ã¹ ãã¤ã³ãã¢ãã¬ã¹ (bindaddr) ã¯IPã¢ãã¬ã¹ã§ãªããã°ãªãã¾ãã ãã¤ã³ããã¼ã ã³ã¼ã«ã¤ãã³ã ãããªãä½¿ç¨ããã«ã¯ãæå¹ãé¸æããä½¿ç¨ããã³ã¼ããã¯ãé¸æãã¦ãã ããã ã³ã¼ããã¯ æ¢ã«ä½¿ç¨ä¸­ï¼INUSEï¼ã®ãµãã¹ã¯ãªãã·ã§ã³ããä¿çãããã¨ãã«ONHOLDãéä¿¡ãããã©ãããBLFãä½¿ç¨ããå ´åã«ä¾¿å©ã æ¢ã«ä½¿ç¨ä¸­ï¼INUSEï¼ã®ãµãã¹ã¯ãªãã·ã§ã³ã«å¥ã®å¼ã³åºããããå ´åã«ãRINGINGãéä¿¡ãããã©ãããBLFãä½¿ç¨ããå ´åã«ä¾¿å©ã ããã©ã«ãã³ã³ãã­ã¹ã ç¡å¹ ãã¤ãããã¯ãã¹ã ãã¤ãããã¯ãã¹ããç©ºã«ãããã¨ã¯ã§ãã¾ããã ãã¤ãããã¯IP ã¨ã©ã¼ è¨­å®ãç·¨é æå¹ å¤é¨IPã¢ãã¬ã¹ ã«ã¼ã¿ã¼ãªã©ã®WANå´å¤é¨éçIPã¢ãã¬ã¹ã¾ãã¯FQDNãï¼å¯¾å¿Asteriskè¨­å®ï¼externipï¼ Fixed å¼·å¶ã¸ãã¿ã¼ãããã¡ã¼ MWI ã®ç¶æå¤æ´ããã§ãã¯ãã¦ããã¢ã«éç¥ããé »åº¦ï¼ç§ï¼ SIPã¯ã©ã¤ã¢ã³ããã¤ãã³ãï¼ä¾ãã°ãä¿çï¼ãèµ·ãããå ´åã«ãããã¼ã¸ã£ã¼ã¤ãã³ããçæããã IPè¨­å® å®è£ ã¸ãã¿ã¼ãããã¡ã¼ã­ã° ã¸ãã¿ã¼ãããã¡ã¼è¨­å® ã­ã¼ã«ã«ãããã¯ã¼ã¯ ã­ã¼ã«ã«ãããã¯ã¼ã¯ãã¹ã¯ãæ­£ãããã©ã¼ãããã§å¥åãã¦ãã ããï¼ä¾: 255.255.255.0 ã¾ãã¯ 24ï¼ã ã­ã¼ã«ã«ãããã¯ã¼ã¯ (localnet) è¨­å®ã¯ãIPã¢ãã¬ã¹ãè¨­å®ããªããã°ãªãã¾ããã ã¡ãã£ã¢ã¨RTPè¨­å® MWIãã¼ãªã³ã°éé æå¤§ãããã¬ã¼ã ãããªã³ã¼ã«ã®æå¤§ãããã¬ã¼ã (kb/s) å¿è¦ã«å¿ãã¦rtf.confã®å¤ãç§»è¡ãã¦åæåãã NAT NATè¨­å® ããã éæ¨æºg726 éç¥ã¨MWI ä¿çéç¥ å¼ã³åºãéç¥ ãã®ä»ã®SIPè¨­å® ãããªãã¯IP RTPãã¼ãç¯å² ã¬ã¸ã¹ãè¨­å® Re-Inviteã®æå è¨­å® %s ã®è¨­å®ã¯ãã¡ãã®è¨­å®ããªã¼ãã¼ã©ã¤ãã§ãã¾ããéè¤ããè¨­å®ãåé¤ãã¦ãã ããã éçIP å¤æ´ãé©ç¨ T38ãã¹ã¹ã«ã¼ ãããªã³ã¼ããã¯ ãããªãµãã¼ã ã¯ã sip.confã®generalã»ã¯ã·ã§ã³ã§è¨­å®ã§ããããã®ä»ã®SIPè¨­å®ãããã§è¨è¿°ã§ãã¾ããè¨­å®ã®ã¨ã©ã¼ãã§ãã¯ã¯è¡ãã¾ããã®ã§ãå¥åå¤ãããç¢ºèªãã¦ãã ãããä»¥ä¸ã®ãã­ã¹ãããã¯ã¹ã«æ¬¡ã®ããã«å¥åãã¾ãï¼<br/> [è¨­å®] = [å¤] <br/>ãã£ã¼ã«ããè¿½å ããã«ã¯ãããã£ã¼ã«ãè¿½å ããã¿ã³ãã¯ãªãã¯ãã¦ãã ãããç©ºã®ããã¯ã¹ã¯è¨­å®é©ç¨æã«åé¤ãã¾ãã æ¢ã«æ¢å­ãã¾ãã ã³ã¼ããã¯è¿½å ä¸­ã«è´å½çãªã¨ã©ã¼ãçºçãã¾ãããã¢ã¸ã¥ã¼ã«ãç¢ºèªãã¦ãã ããã kb/s ããã ããã©ã¼ã«ãã³ã¼ããã¯è¿½å ä¸­... rtpholdtimeoutã¯ãrtptimeoutããå¤§ããå¤ã«è¨­å®ããªããã°ãªãã¾ããã %sã®åã®å¤ãä¿å­ä¸­â¦ ã¯ã 