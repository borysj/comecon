#Remove HTML tags
s|<[^>]*>||g
#Remove Markdown bolds
s|[*]*||g
#Remove Markdown italics
s|[_]*||g
#Replace Markdown links with their names (delete URL)
s|\[\([^]]*\)\]([^)]*)|\1|g
#Replace Markdown reference-style links with their names (delete numbers)
s|\[\([^]]*\)\]\[[^]]*\]|\1|g
#Replace Markdown footnote references
/^\[[^]].*\]:/d
#Remove link attributes
s|{:target=\"blank\"}||g
#Remove Markdown images
/^\!\[/d
#Remove images added with include, keep captions
/^{% include image\.html/d
/^description=\"/d
s|^caption=\"||
s|\" %}$||
