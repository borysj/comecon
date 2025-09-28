<?php
// The quote file defined through $listofQuotes
// consists of one quote per line.
// Every line is of the format "Author|Quote", e.g.
//
// John Doe|I have said something really smart.
// Jane Dee|I have said something even smarter.
//
// In the example path below, the quote file is accessible for everyone.
// Maybe you do want to make your collection of quotes fully visible? However, you
// might also consider hiding it somewhere on the server (outside the public
// folder); for instance if it is large and you do not want bots to download it
// repeatedly spending your transfer quota.

$listOfQuotes = file_get_contents("https://mypage.com/blog/misc/quotes.txt");
if ($listOfQuotes !== false) {
  $quotes = explode("\n", $listOfQuotes);
  $numberOfQuotes = count($quotes);
  $randomQuoteNumber = rand(0, $numberOfQuotes - 1);
  $randomQuote = trim($quotes[$randomQuoteNumber]);
  $randomQuoteElements = explode("|", $randomQuote);
  $author = $randomQuoteElements[0];
  $quotation = $randomQuoteElements[1];
  echo "<div class=\"quotation\">";
  echo "<div id=\"quotationElement\">$quotation</div>";
  echo "<div id=\"authorElement\">$author</div>";
  echo "</div>";
}
?>
