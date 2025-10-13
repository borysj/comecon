<!DOCTYPE html>
<html lang="pl" class="html" data-theme="auto"><head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>
    
      Grawatary
    
  </title>



  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="https://blogrys.pl/assets/images/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://blogrys.pl/assets/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://blogrys.pl/assets/images/favicon/favicon-16x16.png">
  <link rel="manifest" href="https://blogrys.pl/assets/images/favicon/site.webmanifest">
  <link rel="shortcut icon" href="https://blogrys.pl/assets/images/favicon/favicon.ico">
  <!-- Favicon -->

  <!-- Feeds -->
  <link rel="alternate" type="application/atom+xml" href="https://blogrys.pl/feed.xml" title="Blogrys (czołówki wpisów)">
  <link rel="alternate" type="application/atom+xml" href="https://blogrys.pl/feed-full_content.xml" title="Blogrys (pełne wpisy)">
  <link rel="alternate" type="application/atom+xml" href="https://blogrys.pl/assets/commfeeds/comments_newest.xml" title="Blogrys (najnowsze komentarze)">
  <link rel="alternate" type="application/atom+xml" href="https://blogrys.pl/assets/commfeeds/comments_blogpost20250912.xml" title="Komentarze do wpisu Grawatary">
    <!-- Feeds -->

  <link rel="stylesheet" href="https://blogrys.pl/assets/css/main.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Cutive&display=swap" crossorigin>
   <script type="text/javascript">
  window.addEventListener('load', themeChange);
  const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;
  if (currentTheme)
    document.documentElement.setAttribute('data-theme', currentTheme);

  function themeChange() {
    let button = document.querySelector('.theme-toggle');

    button.addEventListener('click', function (e) {
      let currentTheme = document.documentElement.getAttribute('data-theme');
      if (currentTheme === 'dark') {
        transition();
        document.documentElement.setAttribute('data-theme', 'light');
        localStorage.setItem('theme', 'light');
      } else {
        transition();
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
      }
    });

    let transition = () => {
      document.documentElement.classList.add('transition');
      window.setTimeout(() => {
        document.documentElement.classList.remove('transition');
      }, 1000);
    }
  }
</script>


</head>
<body>
    <main class="page-content" aria-label="Content">

<div class="logo-corner">
        <a href="https://blogrys.pl"><b>B</b>logrys</a>
      </div>
<a href="https://blogrys.pl/spisy/alfabet.html">
<button title="Wszystkie wpisy" class="all-entries">
<img id="alphabet" src="https://blogrys.pl/assets/images/abc.svg" width="40" height="40" />
</button>
</a>

<a href="https://blogrys.pl/assets/random_post.php">
<button title="Wylosuj wpis" class="post-randomizer">
<img id="dice" src="https://blogrys.pl/assets/images/dice.svg" width="32" height="32" /> 
</button>
</a>

<button title="Zmień kolor" class="theme-toggle">
  <svg viewBox="0 0 32 32" width="24" height="24" fill="currentcolor">
    <circle cx="16" cy="16" r="14" fill="none" stroke="currentcolor" stroke-width="4"></circle>
    <path d="
             M 16 0
             A 16 16 0 0 0 16 32
             z">
    </path>
  </svg>
</button>


      <div class="w">
        <hgroup>
<h1>Grawatary</h1>
<div class="in-post-info">
	<p><a href="/spisy/kategorie.html#metablog">metablog</a>
	::: 2025-09-12
        ::: 390 słów
	::: #884
        </p>
</div>
</hgroup>



<div class="in-post-text">
<article>
<p>W zamierzchłej, złotej epoce Sieci 2.0 ośrodkiem Twojej tożsamości był awatar.
Zapożyczony z hinduizmu termin oznaczał niewielką graficzną reprezentację nicka.
Był to zatem Twój forumowy portret… który oczywiście żadnym portretem nie był,
gdyż nikt przy zdrowych zmysłach nie identyfikował się w necie prawdziwym
zdjęciem lub choćby narysowaną podobizną (z wyjątkiem <a href="https://polter.pl/user-seji/" class="ext_link" target="_blank">Sejiego</a>).
Internauci wykorzystywali raczej wizerunki bohaterów gier, filmów i anime.
Nierzadko sięgali też po alegoryczne, zagadkowe wyobrażenia w rodzaju pentagramu
otoczonego świeczkami.</p>

<p>Szacownym spadkobiercą awatarowej tradycji są <a href="https://gravatar.com" class="ext_link" target="_blank">grawatary</a>, które połączą
zarejestrowany adres email z wybraną grafiką i poprzez proste jak konstrukcja
cepa API pozwolą każdemu na swobodne pobranie i wyświetlenie dowolnego cyfrowego
konterfektu<sup id="fnref:1"><a href="#fn:1" class="footnote" rel="footnote" role="doc-noteref">1</a></sup>. Mądry ficzur stanowią awatary defaultowe: Jeżeli dany email nie
figuruje w grawatarowej bazie, naszym oczom ukaże się losowa grafika w wybranym
<a href="https://docs.gravatar.com/sdk/images/#:~:text=defaults" class="ext_link" target="_blank">stylu</a>. Co więcej, ten sam email zawsze wygeneruje tę samą grafikę –
przynajmniej do czasu, gdy jego właściciel się zarejestruje i wybierze sobie coś
„prawdziwego”.</p>

<p>Serwis grawatarowy należy do Automattica, właściciela WordPressa. Wolałbym, żeby
zarządzała nim firma mniej <a href="https://wordpress.com/pricing/" class="ext_link" target="_blank">chciwa</a> i <a href="https://en.wikipedia.org/wiki/WP_Engine#WordPress_dispute_and_lawsuit" class="wik_link" target="_blank">kłótliwa</a>, lecz wiem też, że
infrastruktura sama się nie opłaci. Dopóki grawatary będą stosowały się do
wykładni <a href="https://en.wikipedia.org/wiki/Unix_philosophy#Do_One_Thing_and_Do_It_Well" class="wik_link" target="_blank">DOTADIW</a> i dopóki pozostaną darmowe, dopóty nie będę zaglądał
<a href="https://en.wikipedia.org/wiki/Matt_Mullenweg" class="wik_link" target="_blank">Mattowi Mullenwegowi</a> w zęby. Raczej pochwalę łatwość, z jaką przychodzi
ich (grawatarów, nie zębów) zintegrowanie z inną witryną.</p>

<p class="in-post-textbox">Począwszy od niniejszego wpisu, <a href="https://blogrys.pl/2024/04/26/comecon/index.php" target="_blank">Comecon</a>, czyli Blogrysowy system
komentarzy, współpracuje z grawatarami. Dowiecie się nareszcie, że autorem bloga
jest zadziwiona małpa.</p>

<p>Od strony technicznej dodanie grawatarów powinno być dziecinnie proste:
Wystarczy przecież uzupełnić skrypt PHP odpowiedzialny za wyświetlanie
komentarzy o kilka linijek, które postawią obok nicka grawatarową grafikę.
Niestety, dwa lata temu źle zaplanowałem architekturę Comeconu. On w ogóle nie
zapisywał adresów mailowych komentujących! Emaile służyły jak dotąd wyłącznie do
subskrybcji komentarzy, dodawane więc były do osobnej bazy<sup id="fnref:2"><a href="#fn:2" class="footnote" rel="footnote" role="doc-noteref">2</a></sup>.</p>

<p>Musiałem zatem rozszerzyć rejestr komentarzy o dodatkowe pole, a następnie
zmodyfikować skrypty odpowiedzialne za ich zapisywanie i edycję. Co więcej,
teraz podstawową rolę adresu mailowego będzie sprowadzenie grawatara.
Subskrybcja mailowa musi usunąć się na drugi plan, więc do formularza dodałem
stosowny „czekboks”. Należało to zgrać jeszcze z bazą <a href="https://blogrys.pl/rejestracja.html" target="_blank">zarejestrowanych</a>
komentatorów – tak, żeby email pobierany był z niej zawsze w celu wizerunkowym.</p>

<p>Powinno działać.</p>

<div class="footnotes" role="doc-endnotes">
  <ol>
    <li id="fn:1">
      <p>Pod warunkiem znajomości jego lub jej adresu, a raczej hasza tegoż. <a href="#fnref:1" class="reversefootnote" role="doc-backlink">&#8617;</a></p>
    </li>
    <li id="fn:2">
      <p>Właściwie: do osobnej kategorii plików. Comecon nie działa wcale w oparciu o bazę danych z prawdziwego zdarzenia, ale korzysta z płaskich plików tekstowych, de facto z formatu CSV. <a href="#fnref:2" class="reversefootnote" role="doc-backlink">&#8617;</a></p>
    </li>
  </ol>
</div>

</article>
<p><br><br></p>
<?php
$postURI = $_SERVER["REQUEST_URI"];
if (str_contains($postURI, "index.php")) {
    $a = -10;
} else {
    if (substr($postURI, -1) !== "/") { $postURI = $postURI . "/"; }
    $a = -1;
}
$commentFile = str_replace("/", "-", substr($postURI, 1, $a) . "-COMMENTS.txt");
$commentFilePath = "/home/blogryst/data/comments/" . $commentFile;
if (file_exists($commentFilePath)) {
?>
    <p><br><br><br></p>
    <div class="comments">
    <h2>Komentarze</h2>
<?php
    $comments = explode(PHP_EOL, file_get_contents($commentFilePath));
    foreach ($comments as $key => $c) {
        if (!empty($c)) {
            $cc = explode("<|>", $c);
            $commentAnchor = str_replace(array(" ", "-", ":"), "", $cc[1]);
            if (!empty($cc[3])) {
                $nick = '<a href="' . $cc[3] . '">' . $cc[2] . '</a>';
            } else {
                $nick = $cc[2];
            }
            if ($key === array_key_last($comments) - 1) {
?>
            <a id="lastComment"></a>
<?php
            }
            $hashedEmail = $cc[4];
?>
            <a id="<?=$commentAnchor?>"></a>
            <p class="comm_author<?=$cc[6]?>">
            <img class ="gravatar"
                 src="https://www.gravatar.com/avatar/<?=$hashedEmail?>?s=40&d=retro"
                 alt="Gravatar">
            <b><?=$nick?></b>&nbsp;(<?=$cc[1]?>)</p>
<?php
            $cookieName = $cc[0] . "<|>" . str_replace(array("-", " ", ":"), "", $cc[1]);
            if (isset($_COOKIE[$cookieName])) {
                $pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
                if (preg_match($pattern, $cc[0], $matches)) {
                    $year = $matches[1];      $month = $matches[2];
                    $day = $matches[3];       $title = $matches[4];
                }
                $dateDashed = $year . "-" . $month . "-" . $day;
?>
                <p class="comm_author_edit<?=$cc[6]?>">
                <a href="https://blogrys.pl/assets/edit_comment.php?d=<?=$dateDashed?>&c=<?=$_COOKIE[$cookieName]?>">
                ..:: Masz 20 minut na edycję komentarza ::..
                </a></p>
<?php
            }
?>
            <p class="comm_content<?=$cc[6]?>"><?=$cc[5]?></p>
<?php
        }
    }
?>
    </div>
<?php
}
?>

<p></p>
<p><br><br></p>
<div class="commentFormCSS" style="text-align: center;">
<form name="commentForm" action="https://blogrys.pl/assets/save_comment2.php" method="POST" onsubmit="submitButton.disabled = true; return true;">
<fieldset>
<legend align="left">C O M E C O N</legend>
<input type="hidden" id="url" name="url">

<label for="comment">Zostaw swoją glosę (maks. 4000 znaków):</label>
<textarea id="comment" name="comment" maxlength="3900"
 placeholder="Dodawaj linki tak: [Przykład](www.example.com).
Działa też **pogrubienie** i *kursywa*.
Kod i nazwy plików możesz wyróżniać `w ten sposób`.
Akapituj śmiało, nowe linie i puste linie zostaną zachowane.
Jeżeli masz włączone ciasteczka, będziesz mógł edytować komentarz."
 required>
</textarea>

<label for="name">Podpis:</label>
<input id="name" name="name" required>

<label for="password">Hasło (jeżeli jesteś <a href="https://blogrys.pl/rejestracja.html">zarejestrowany</a>):</label>
<input id="password" name="password" type="password">

<label for="webpage">Twoja strona:</label>
<input id="webpage" name="webpage"
 placeholder="Pomiń śmiało początkowe http(s):// w adresie.">

<label for="email">Twój email (dla <a href="https://gravatar.com">gravatara</a> i ewentualnie powiadomień):</label>
<input id="email" name="email" type="email"
 placeholder="Możesz zostawić puste">

<label for="email2">
    <input id="email-comments" name="email-comments" type="checkbox">
    Odhacz tutaj, jeżeli chcesz prenumerować komentarze mailem (zamiast
    <a href="https://blogrys.pl/assets/commfeeds/comments_blogpost20250912.xml">feedem</a>) </label>

<label for="captcha">
    Biedna kapcza:<br>w kótrm rku s&omicron;kńzcył&alpha; śę<br>pirszew&alpha; wn&omicron;j&alpha; śwtw&alpha;?<br><br>
</label>

<input id="captcha" name="captcha"
placeholder="####">

<input type="submit" name="submitButton" value="Wyślij">
</fieldset>
</form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
        var urlField = document.getElementById("url");
        var currentURL = window.location.href;
        urlField.value = currentURL;
        });
</script>

<p></p>
</div>

<footer>
<nav>
<div class="in-post-tags"><p>
<a href="/spisy/tagi.html#grafika">grafika</a>
</p></div>

<div class="post-navigation">
  <a class="prev" href="/2025/08/14/wladca-kotwicy/index.php">&laquo; Władca kotwicy</a>
  </div>
</nav>
        <div class="credits">
            <p>Blogrys powstał 5 października 2006 r. 
<br>
<a href="https://blogrys.pl/silnik.html">Obecne wcielenie blogu</a> zostało wygenerowane statycznie w <a href="https://jekyllrb.com/" target="_blank" rel="noreferrer">Jekyllu</a>.<br />
            Blogrys powstaje bez najmniejszego udziału AI/LLM.<br />
            Design oparty jest o <a href="https://github.com/abhinavs/moonwalk" target="_blank" rel="noreferrer">Moonwalk</a> (&copy;&nbsp;Abhinav Saxena).</p>
	</div>
  
	<div class="search-container">
        <form name="searchForm" action="https://blogrys.pl/assets/search.php" method="POST" target="_blank">
			<input type="text" id="searchPhrase" name="searchPhrase" placeholder="Szukana fraza" required>
			<input type="submit" value="Szukaj">
		</form>
	</div>

</footer>

      </div>
    </main>
  </body>
</html>
