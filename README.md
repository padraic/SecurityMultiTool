SecurityMultiTool
=================

A multitool library offering access to recommended security related libraries, standardised implementations of security defences, and secure implementations of commonly performed tasks.

The purpose of the library is to serve as both a useful set of utilities and to act as a set of reference implementations which can be learned from. It may be used by applications regardless of whether they are web application framework based or not. The use of a web application framework does not guarantee your security.

Yet Another Security Library?
-----------------------------

There are actually few security related metapackages available in PHP and many are outdated and/or insecure. Feeding this problem is a lack of concrete information about best practices in PHP. SecurityMultiTool extracts source code, patterns and best practices from a variety of sources to offer a singular point of reference. The source code will be opinionated. For example, SecurityMultiTool\Html\Sanitizer uses HTMLPurifier and does not allow for that dependency to be substituted (because there is NO other secure HTML sanitizer in PHP!).

You may choose to use SecurityMultiTool as a dependency in your projects. You can use it as a useful set of examples of what you should be doing. You can use it as a benchmark to check if your own code and its dependencies are straying from the recommended path. You can copy and paste the code to fit your needs (and I won't go beserk if you don't attribute me). You can pass around URLs to the code, if useful, to recommend improved practices to others.

I'm more than happy to accept PRs for new features with the understanding that they should be rigorously tested, provably secure and in compliance with secure practices.

Current Features
----------------

The following features are available and tested as of 18 March 2013:

1. HTML Output Escaping (SecurityMultiTool\Html\Escaper)
2. HTML Sanitization (SecurityMultiTool\Html\Sanitizer)
3. Random Number/Bytes Generator (SecurityMultiTool\Random\Generator)
4. HTTP Strict-Transport-Security & X-CSRFToken Headers (SecurityMultiTool\Http\Header)
5. HTTPS Detector (SecurityMultiTool\Http\HttpsDetector)
6. Sanitized Markdown and BBCode Parsers (SecurityMultiTool\Markdown|BBcode\Parser)
7. Anti Timing-Attack String Comparison (SecurityMultiTool\String\FixedTimeComparison)

The following libraries are dependencies installed with SecurityMultiTool which you may use independently of SecurityMultiTool:

* HTMLPurifier http://www.htmlpurifier.org
* RandomLib https://github.com/ircmaxell/RandomLib

There is a lot more to come! 

Reporting Security Vulnerabilities
----------------------------------

If you locate a potential vulnerability in the source code, you should report it directly to padraic.brady@gmail.com. I undertake to resolve any such reports within 30 days of receipt and I will confirm receipt of any report within 3 days. Any resolving source code will be made available to the reporter for review prior to it being committed to this repository. You are free to publicly disclose any vulnerability, once fixed or after any period you require when sending a report, as you should already know.