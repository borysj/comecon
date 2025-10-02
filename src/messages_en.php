---
layout: null
---
<?php
const $EXITMSG_BADCOMMENTCAPTCHA = "I suspect you of being a robot.";
const $EXITMSG_WRONGPASSWORD = "Your comment has not been added.<br>You have used a reserved nickname in a wrong way.";
const $EXITMSG_ERRORURL = "An error occurred while reading the address of the page<br>where the comment was supposed to be added.";
const $EXITMSG_DUPLICATE = "This comment is a duplicate.<br>Probably you have clicked a button several times too quickly<br>or tried to refresh a webpage right after adding it.";
const $EXITMSG_ERRORSAVINGCOMMENT = "An error occurred while saving your comment.<br>Go back and try again.<br>If the error persists, please send a message to {$settings['email']['blogContactMail']}";
const $EXITMSG_ERRORRUNNINGCOMMENTSCRIPT = "An error occurred while activating the comment script.<br>>Go back to the comment form and try again.<br>If the error persists, please send a message to {$settings['email']['blogContactMail']}";
const $EXITMSG_BADCAPTCHAEMAIL = "You are supposed to add the antibot code to your email. Read carefully the instruction right above the form.";
const $EXITMSG_EMAILNOTFOUND = "I have not found this email on the subscriber list.";
const $EXITMSG_SUBSCRIBERLISTNOTFOUND = "The subscriber list has not been found at all.";
const $EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT = "The script has not been activated.<br>Are you sure that the link for cancelling subscription is valid?<br>Where did it come from?";
const $EXITMSG_REMOVEDSUBSCRIBER = " has been removed from the subscriber list ";
const $EXITMSG_CANNOTREMOVESUBSCRIBER = "The passwords do not match. I cannot remove ";
const $EXITMSG_WRONGCOMMENTADMINPASSWORD = "The admin password for editing comments is wrong.";
const $EXITMSG_WRONGCOMMENTID = "An error has occurred. I have not found the comment to edit. The timestamp and/or the comment code in the link must be wrong.";
const $EXITMSG_TOOLATETOEDITCOMMENT = "This train has left. It is too late to edit this comment.";
const $EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD = "Are you trying to hack me?";
const $EXITMSG_NOSEARCHPHRASE = "No search phrase has been provided";
const $MSG_COMMENTINCONTEXT = "The comments could have been changed or deleted in the meanwhile. Follow the link to the blog to find the most recent version.";
const $MSG_COMMENTFEEDENTRYTITLE = "Comment for the blog post";
const $LABEL_EDITCOMMENTTITLE = "Edit comment";
const $LABEL_EDITCOMMENTFIELD = "Edit your comment.<br>If you wish to remove it, delete everything and confirm using the button.";
const $LABEL_EDITCOMMENTBUTTON = "Confirm changes";
const $LABEL_SEARCHTITLE = "Searched phrase";
const $LABEL_SEARCHRESULT = "Number of pages with the searched phrase";
const $LABEL_SEARCHRESULTCOMMENTS = "Number of pages where the searched phrase is in the comments";
