#Remove HTML tags
s|<[^>]*>||g
#Remove Markdown bolds
s| \*\*| |g
s|\*\*[ .:;-]| |g
#Remove Markdown italics
s| _| |g
s|_[ .:;-]| |g
#Remove Markdown bullet points
s|^\* ||g
#Remove Markdown code markers
s|`||g
#Replace Markdown links with their names (delete URL)
s|\[\([^]]*\)\]([^)]*)|\1|g
#Replace Markdown reference-style links with their names (delete numbers)
s|\[\([^]]*\)\]\[[^]]*\]|\1|g
#Remove Markdown footnote labels
s|\[\^[0-9]*\]||g
s|^: |\n|
#Replace Markdown URL references
/^\[[^]]*\]: /d
#Remove link attributes
s|{:[^}]*}||g
#Remove Markdown images
/^\!/d
#Remove images added with include, keep captions
/^{% include image\.html/d
/^description=\"/d
s|^caption=\"||
s|\" %}$||
# Remove code snippets
/^{% highlight/,/{% endhighlight %}/d
# Remove multiple spaces
s|[ ]\+| |g
