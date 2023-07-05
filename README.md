# HNP-COOKIE-SQL-BASED-AUTHENTICATION-SYSTEM
COOKIE &amp; SQL BASED AUTHENTICATION SYSTEM


Our approach is based on a SQL-based AUTH method that uses cookies (tokens), as traditional cookies do not work across servers or domains and should not be used for login or session functions due to security reasons. Here is a description of how our approach works:

    When a user clicks on a specific button on Domain A/Server 1, cookies with unique token values are created. These token values are stored in the user's browser and also saved in a SQL database.

    These token values are then passed to Domain B/Server 2. There, they are again checked and verified by searching the database for the token value.

    If the token and cookie are correct and match in the database, an identical cookie is also created on Domain B. This synchronizes the cookies between the domains. This synchronized cookie can then be used for sessions, the login process, or other cookie-based functions on Domain B.

Through this method, we enable secure and effective use of cookies for login and session functions without the usual limitations of traditional cookies.

It works on all systems/websites since no system-specific tags/hooks are used. It could easily be converted into a WordPress plugin. The current authentication system operates with the use of 2 domains/servers, but it would be possible to add an additional server/domain in between (AUTH-SERVER/AUTH-DOMAIN). This would allow for multiple encryptions, providing extremely high security.

Now:

![logic-A-1 (1)](https://github.com/hnp-chris/HNP-COOKIE-SQL-BASED-AUTHENTICATION-SYSTEM/assets/138715217/17c06fe4-7890-4c33-bf41-45148a8b7103)


Later: 

![logic1 (1)](https://github.com/hnp-chris/HNP-COOKIE-SQL-BASED-AUTHENTICATION-SYSTEM/assets/138715217/32dd700c-25c3-4179-b18e-fb162bc47fc3)



Roadmap (coming soon):
Version 1.1

New features:

    OpenSSL encryption for token & cookie
    Automatic removal of token from the database
    CSS styling
    Version 1.2

New features:

    Extend logic to 4 points (additional index.html for entry point)
    Expand CSS styling
    Version 1.3

New features:

    Add optional AUTH server/AUTH domain mid-point
    Additional encryption with different encryption methods
    Version 2.0

New features:

    Final styling
    Use $_POST instead of $_GET
    Additional data can be "sent along" (such as usernames, passwords, age, etc.)
    Additional data will be encrypted.
