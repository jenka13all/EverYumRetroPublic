EverYumRetro
============
"EverYumRetro" is an PHP web app created with 2 friends for the Evernote/Telekomm Hackathon hosted in Berlin in April 2013.  It uses a mashup of APIs, including EverNote, the Yumly online recipe database, and the TelekomTropo API.

Evernote is used as the persistence layer, where a user can write a list of what they have at home in their kitchen.
The user can then literally call up the application through the TelekomTropo API, either with a smartphone or an old fashioned Nokia - which is what we used.

Once the user is connected, they are asked over the phone about what kind of cuisine they feel like eating that night, and can answer with such responses as "Mexican", "Indian", "Chinese", etc.

The app then grabs their data from Evernote and sends out a query to the Yumly online recipe database, looking for the recipes that match most of the criteria on the list and the user's preferred cuisine.  It sends back 3 of the best-matching recipes as text-messages, as a list of ingredients that still need to be bought.

A future release would save the recipes, with instructions and photos, directly to the user's Evernote account.

We had to rename the app to "EasyChef", but otherwise got it to work, and ended up winning the Evernote/Telekomm mashup prize!  
