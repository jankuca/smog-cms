<html>
<head>
	<title>{SITE_HEADER}</title>
</head>
<body>
<h1>{SITE_HEADER}</h1>
<p>{SITE_SLOGAN}</p>

<ul id="menu">
<loop(aaa)>	<li><a<if(aaa.ACTIVE)> style="color:#906;"<else(aaa.ACTIVE)> style="font-style:italic;"</if(aaa.ACTIVE)> href="<var(key1)>"><var(key2)raw></a><ul>
<loop(b)>		<li><a<if(aaa.ACTIVE)> style="color:#906;"<else(aaa.ACTIVE)> style="font-style:italic;"</if(aaa.ACTIVE)> href="<var(var)>"><var(another)></a></li>
</loop(b)>	</ul></li>
</loop(aaa)></ul>
Počet položek menu: <count(aaa)><br /><br />

<if(CONDITION)>Splněno<else(CONDITION)>Nesplněno</if(CONDITION)><br />
--<br />
<if(!CONDITION)>Nesplněno<else(!CONDITION)>Splněno</if(!CONDITION)><br />
--<br />
<if(CONDITION)>Splněno</if(CONDITION)>
<if(!CONDITION)>Nesplněno</if(!CONDITION)><br />

<loop(LOOP)><var(var)> = <var(another)></loop(LOOP)>
</body>
</html>
