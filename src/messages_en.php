<?php

define('EXITMSG_BADCOMMENTCAPTCHA', "I suspect you of being a robot.");
define('EXITMSG_WRONGPASSWORD', "Your comment has not been added.<br>
        You have used a reserved nickname in a wrong way.");
define('EXITMSG_ERRORURL', "An error occurred while reading the address of the page<br>
        where the comment was supposed to be added.");
define('EXITMSG_DUPLICATE', "This comment is a duplicate.<br>Probably you have clicked
        a button several times too quickly<br>or tried to refresh a webpage right after adding it.");
define('EXITMSG_ERRORSAVINGCOMMENT', "An error occurred while saving your comment.<br>
        Go back and try again.<br>If the error persists, please send a message to
        {$settings['email']['blogContactMail']}");
define('EXITMSG_ERRORRUNNINGCOMMENTSCRIPT', "An error occurred while activating the comment script.<br>
        Go back to the comment form and try again.<br>If the error persists, please send
        a message to {$settings['email']['blogContactMail']}");
define('EXITMSG_BADCAPTCHAEMAIL', "You are supposed to add the antibot code to your email.
        Read carefully the instruction right above the form.");
define('EXITMSG_EMAILNOTFOUND', "I have not found this email on the subscriber list.");
define('EXITMSG_SUBSCRIBERLISTNOTFOUND', "The subscriber list has not been found at all.");
define('EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT', "The script has not been activated.<br>
        Are you sure that the link for cancelling subscription is valid?<br>Where did it come from?");
define('EXITMSG_REMOVEDSUBSCRIBER', " has been removed from the subscriber list ");
define('EXITMSG_CANNOTREMOVESUBSCRIBER', "The passwords do not match. I cannot remove ");
define('EXITMSG_WRONGCOMMENTADMINPASSWORD', "The admin password for editing comments is wrong.");
define('EXITMSG_WRONGCOMMENTID', "An error has occurred. I have not found the comment to edit.
        The timestamp and/or the comment code in the link must be wrong.");
define('EXITMSG_TOOLATETOEDITCOMMENT', "This train has left. It is too late to edit this comment.");
define('EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD', "Are you trying to hack me?");
define('EXITMSG_NOSEARCHPHRASE', "No search phrase has been provided");
define('EXITMSG_INVALIDACTION', "Invalid Comecon action");
define('EXITMSG_FILEUNREADABLE', "The file is unreadable");
define('EXITMSG_NOTSTRING', "A string was expected");
define('EXITMSG_WRONGREQUESTMETHOD', "The request method (GET or POST) is wrong");
define('EXITMSG_KEYISWRONG', "A key is not set or not a string");
define('EXITMSG_NOTIFICATIONERROR', "I cannot create an email notification due to wrong parameters");
define('MSG_COMMENTINCONTEXT', "The comments could have been changed or deleted in the meanwhile.
        Follow the link to the blog to find the most recent version.");
define('MSG_COMMENTFEEDENTRYTITLE', "Comment for the blog post");
define('LABEL_EDITCOMMENTTITLE', "Edit comment");
define('LABEL_EDITCOMMENTFIELD', "Edit your comment.<br>If you wish to remove it,
        delete everything and confirm using the button.");
define('LABEL_EDITCOMMENTBUTTON', "Confirm changes");
define('LABEL_SEARCHTITLE', "Searched phrase");
define('LABEL_SEARCHRESULT', "Number of pages with the searched phrase");
define('LABEL_SEARCHRESULTCOMMENTS', "Number of pages where the searched phrase is in the comments");
