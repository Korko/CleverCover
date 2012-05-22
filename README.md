Clever Cover
============

WebApplication to simply create both cover and profile picture
for social networks like Facebook or Google Plus.

Home
----

This part of the program is just a simple integration that will redirect
to the core of the program. It's like a GUI to execute the real CleverCover.

Core
----

This is the real core where you can integrate, manipulate and create
your own cover and avatar.

### Parameters

There are few parameters, all are GET:
* site: enum(facebook, google) - Just an information about which type of cover you want, will change the design and the properties (width/position etc)
* cover: string - URL of the cover picture.
* avatar: string (optional) - URL of the profile picture. If not specified, the cover picture will be used and not splitted.

### Split

If only a cover url is specified, this image will be used for both cover and avatar as a single big picture.
By specifying the avatar url, you can split both parts and move, resize each independantly.


