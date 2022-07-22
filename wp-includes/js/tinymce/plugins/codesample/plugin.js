!(function (u) {
    "use strict";
    var n = function (e) {
            var t = e,
                a = function () {
                    return t;
                };
            return {
                get: a,
                set: function (e) {
                    t = e;
                },
                clone: function () {
                    return n(a());
                },
            };
        },
        e = tinymce.util.Tools.resolve("tinymce.PluginManager"),
        i = tinymce.util.Tools.resolve("tinymce.dom.DOMUtils"),
        s = function (e) {
            return e.settings.codesample_content_css;
        },
        a = function (e) {
            return e.settings.codesample_languages;
        },
        o = function (e) {
            return Math.min(i.DOM.getViewPort().w, e.getParam("codesample_dialog_width", 800));
        },
        l = function (e) {
            return Math.min(i.DOM.getViewPort().w, e.getParam("codesample_dialog_height", 650));
        },
        t = {},
        r = t,
        g = void 0 !== t ? t : "undefined" != typeof WorkerGlobalScope && u.self instanceof WorkerGlobalScope ? u.self : {},
        c = (function () {
            var c = /\blang(?:uage)?-(?!\*)(\w+)\b/i,
                S = (g.Prism = {
                    util: {
                        encode: function (e) {
                            return e instanceof o
                                ? new o(e.type, S.util.encode(e.content), e.alias)
                                : "Array" === S.util.type(e)
                                    ? e.map(S.util.encode)
                                    : e
                                        .replace(/&/g, "&amp;")
                                        .replace(/</g, "&lt;")
                                        .replace(/\u00a0/g, " ");
                        },
                        type: function (e) {
                            return Object.prototype.toString.call(e).match(/\[object (\w+)\]/)[1];
                        },
                        clone: function (e) {
                            switch (S.util.type(e)) {
                                case "Object":
                                    var t = {};
                                    for (var a in e) e.hasOwnProperty(a) && (t[a] = S.util.clone(e[a]));
                                    return t;
                                case "Array":
                                    return (
                                        e.map &&
                                        e.map(function (e) {
                                            return S.util.clone(e);
                                        })
                                    );
                            }
                            return e;
                        },
                    },
                    languages: {
                        extend: function (e, t) {
                            var a = S.util.clone(S.languages[e]);
                            for (var n in t) a[n] = t[n];
                            return a;
                        },
                        insertBefore: function (a, e, t, n) {
                            var i = (n = n || S.languages)[a];
                            if (2 === arguments.length) {
                                for (var r in (t = e)) t.hasOwnProperty(r) && (i[r] = t[r]);
                                return i;
                            }
                            var s = {};
                            for (var o in i)
                                if (i.hasOwnProperty(o)) {
                                    if (o === e) for (var r in t) t.hasOwnProperty(r) && (s[r] = t[r]);
                                    s[o] = i[o];
                                }
                            return (
                                S.languages.DFS(S.languages, function (e, t) {
                                    t === n[a] && e !== a && (this[e] = s);
                                }),
                                    (n[a] = s)
                            );
                        },
                        DFS: function (e, t, a) {
                            for (var n in e) e.hasOwnProperty(n) && (t.call(e, n, e[n], a || n), "Object" === S.util.type(e[n]) ? S.languages.DFS(e[n], t) : "Array" === S.util.type(e[n]) && S.languages.DFS(e[n], t, n));
                        },
                    },
                    plugins: {},
                    highlightAll: function (e, t) {
                        for (var a = u.document.querySelectorAll('code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'), n = 0, i = void 0; (i = a[n++]); ) S.highlightElement(i, !0 === e, t);
                    },
                    highlightElement: function (e, t, a) {
                        for (var n, i, r = e; r && !c.test(r.className); ) r = r.parentNode;
                        r && ((n = (r.className.match(c) || [, ""])[1]), (i = S.languages[n])),
                            (e.className = e.className.replace(c, "").replace(/\s+/g, " ") + " language-" + n),
                            (r = e.parentNode),
                        /pre/i.test(r.nodeName) && (r.className = r.className.replace(c, "").replace(/\s+/g, " ") + " language-" + n);
                        var s = e.textContent,
                            o = { element: e, language: n, grammar: i, code: s };
                        if (s && i)
                            if ((S.hooks.run("before-highlight", o), t && g.Worker)) {
                                var l = new u.Worker(S.filename);
                                (l.onmessage = function (e) {
                                    (o.highlightedCode = e.data), S.hooks.run("before-insert", o), (o.element.innerHTML = o.highlightedCode), a && a.call(o.element), S.hooks.run("after-highlight", o), S.hooks.run("complete", o);
                                }),
                                    l.postMessage(JSON.stringify({ language: o.language, code: o.code, immediateClose: !0 }));
                            } else
                                (o.highlightedCode = S.highlight(o.code, o.grammar, o.language)),
                                    S.hooks.run("before-insert", o),
                                    (o.element.innerHTML = o.highlightedCode),
                                a && a.call(e),
                                    S.hooks.run("after-highlight", o),
                                    S.hooks.run("complete", o);
                        else S.hooks.run("complete", o);
                    },
                    highlight: function (e, t, a) {
                        var n = S.tokenize(e, t);
                        return o.stringify(S.util.encode(n), a);
                    },
                    tokenize: function (e, t, a) {
                        var n = S.Token,
                            i = [e],
                            r = t.rest;
                        if (r) {
                            for (var s in r) t[s] = r[s];
                            delete t.rest;
                        }
                        e: for (var s in t)
                            if (t.hasOwnProperty(s) && t[s]) {
                                var o = t[s];
                                o = "Array" === S.util.type(o) ? o : [o];
                                for (var l = 0; l < o.length; ++l) {
                                    var c = o[l],
                                        u = c.inside,
                                        g = !!c.lookbehind,
                                        d = 0,
                                        p = c.alias;
                                    c = c.pattern || c;
                                    for (var f = 0; f < i.length; f++) {
                                        var h = i[f];
                                        if (i.length > e.length) break e;
                                        if (!(h instanceof n)) {
                                            c.lastIndex = 0;
                                            var m = c.exec(h);
                                            if (m) {
                                                g && (d = m[1].length);
                                                var b = m.index - 1 + d,
                                                    y = b + (m = m[0].slice(d)).length,
                                                    v = h.slice(0, b + 1),
                                                    k = h.slice(y + 1),
                                                    w = [f, 1];
                                                v && w.push(v);
                                                var x = new n(s, u ? S.tokenize(m, u) : m, p);
                                                w.push(x), k && w.push(k), Array.prototype.splice.apply(i, w);
                                            }
                                        }
                                    }
                                }
                            }
                        return i;
                    },
                    hooks: {
                        all: {},
                        add: function (e, t) {
                            var a = S.hooks.all;
                            (a[e] = a[e] || []), a[e].push(t);
                        },
                        run: function (e, t) {
                            var a = S.hooks.all[e];
                            if (a && a.length) for (var n = 0, i = void 0; (i = a[n++]); ) i(t);
                        },
                    },
                }),
                o = (S.Token = function (e, t, a) {
                    (this.type = e), (this.content = t), (this.alias = a);
                });
            if (
                ((o.stringify = function (t, a, e) {
                    if ("string" == typeof t) return t;
                    if ("Array" === S.util.type(t))
                        return t
                            .map(function (e) {
                                return o.stringify(e, a, t);
                            })
                            .join("");
                    var n = { type: t.type, content: o.stringify(t.content, a, e), tag: "span", classes: ["token", t.type], attributes: {}, language: a, parent: e };
                    if (("comment" === n.type && (n.attributes.spellcheck = "true"), t.alias)) {
                        var i = "Array" === S.util.type(t.alias) ? t.alias : [t.alias];
                        Array.prototype.push.apply(n.classes, i);
                    }
                    S.hooks.run("wrap", n);
                    var r = "";
                    for (var s in n.attributes) r += (r ? " " : "") + s + '="' + (n.attributes[s] || "") + '"';
                    return "<" + n.tag + ' class="' + n.classes.join(" ") + '" ' + r + ">" + n.content + "</" + n.tag + ">";
                }),
                    !g.document)
            )
                return (
                    g.addEventListener &&
                    g.addEventListener(
                        "message",
                        function (e) {
                            var t = JSON.parse(e.data),
                                a = t.language,
                                n = t.code,
                                i = t.immediateClose;
                            g.postMessage(S.highlight(n, S.languages[a], a)), i && g.close();
                        },
                        !1
                    ),
                        g.Prism
                );
        })();
    void 0 !== r && (r.Prism = c),
        (c.languages.markup = {
            comment: /<!--[\w\W]*?-->/,
            prolog: /<\?[\w\W]+?\?>/,
            doctype: /<!DOCTYPE[\w\W]+?>/,
            cdata: /<!\[CDATA\[[\w\W]*?]]>/i,
            tag: {
                pattern: /<\/?[^\s>\/=.]+(?:\s+[^\s>\/=]+(?:=(?:("|')(?:\\\1|\\?(?!\1)[\w\W])*\1|[^\s'">=]+))?)*\s*\/?>/i,
                inside: {
                    tag: { pattern: /^<\/?[^\s>\/]+/i, inside: { punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/ } },
                    "attr-value": { pattern: /=(?:('|")[\w\W]*?(\1)|[^\s>]+)/i, inside: { punctuation: /[=>"']/ } },
                    punctuation: /\/?>/,
                    "attr-name": { pattern: /[^\s>\/]+/, inside: { namespace: /^[^\s>\/:]+:/ } },
                },
            },
            entity: /&#?[\da-z]{1,8};/i,
        }),
        c.hooks.add("wrap", function (e) {
            "entity" === e.type && (e.attributes.title = e.content.replace(/&amp;/, "&"));
        }),
        (c.languages.xml = c.languages.markup),
        (c.languages.html = c.languages.markup),
        (c.languages.mathml = c.languages.markup),
        (c.languages.svg = c.languages.markup),
        (c.languages.css = {
            comment: /\/\*[\w\W]*?\*\//,
            atrule: { pattern: /@[\w-]+?.*?(;|(?=\s*\{))/i, inside: { rule: /@[\w-]+/ } },
            url: /url\((?:(["'])(\\(?:\r\n|[\w\W])|(?!\1)[^\\\r\n])*\1|.*?)\)/i,
            selector: /[^\{\}\s][^\{\};]*?(?=\s*\{)/,
            string: /("|')(\\(?:\r\n|[\w\W])|(?!\1)[^\\\r\n])*\1/,
            property: /(\b|\B)[\w-]+(?=\s*:)/i,
            important: /\B!important\b/i,
            function: /[-a-z0-9]+(?=\()/i,
            punctuation: /[(){};:]/,
        }),
        (c.languages.css.atrule.inside.rest = c.util.clone(c.languages.css)),
    c.languages.markup &&
    (c.languages.insertBefore("markup", "tag", {
        style: { pattern: /<style[\w\W]*?>[\w\W]*?<\/style>/i, inside: { tag: { pattern: /<style[\w\W]*?>|<\/style>/i, inside: c.languages.markup.tag.inside }, rest: c.languages.css }, alias: "language-css" },
    }),
        c.languages.insertBefore(
            "inside",
            "attr-value",
            {
                "style-attr": {
                    pattern: /\s*style=("|').*?\1/i,
                    inside: { "attr-name": { pattern: /^\s*style/i, inside: c.languages.markup.tag.inside }, punctuation: /^\s*=\s*['"]|['"]\s*$/, "attr-value": { pattern: /.+/i, inside: c.languages.css } },
                    alias: "language-css",
                },
            },
            c.languages.markup.tag
        )),
        (c.languages.clike = {
            comment: [
                { pattern: /(^|[^\\])\/\*[\w\W]*?\*\//, lookbehind: !0 },
                { pattern: /(^|[^\\:])\/\/.*/, lookbehind: !0 },
            ],
            string: /(["'])(\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/,
            "class-name": { pattern: /((?:\b(?:class|interface|extends|implements|trait|instanceof|new)\s+)|(?:catch\s+\())[a-z0-9_\.\\]+/i, lookbehind: !0, inside: { punctuation: /(\.|\\)/ } },
            keyword: /\b(if|else|while|do|for|return|in|instanceof|function|new|try|throw|catch|finally|null|break|continue)\b/,
            boolean: /\b(true|false)\b/,
            function: /[a-z0-9_]+(?=\()/i,
            number: /\b-?(?:0x[\da-f]+|\d*\.?\d+(?:e[+-]?\d+)?)\b/i,
            operator: /--?|\+\+?|!=?=?|<=?|>=?|==?=?|&&?|\|\|?|\?|\*|\/|~|\^|%/,
            punctuation: /[{}[\];(),.:]/,
        }),
        (c.languages.javascript = c.languages.extend("clike", {
            keyword: /\b(as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|false|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|true|try|typeof|var|void|while|with|yield)\b/,
            number: /\b-?(0x[\dA-Fa-f]+|0b[01]+|0o[0-7]+|\d*\.?\d+([Ee][+-]?\d+)?|NaN|Infinity)\b/,
            function: /[_$a-zA-Z\xA0-\uFFFF][_$a-zA-Z0-9\xA0-\uFFFF]*(?=\()/i,
        })),
        c.languages.insertBefore("javascript", "keyword", { regex: { pattern: /(^|[^/])\/(?!\/)(\[.+?]|\\.|[^/\\\r\n])+\/[gimyu]{0,5}(?=\s*($|[\r\n,.;})]))/, lookbehind: !0 } }),
        c.languages.insertBefore("javascript", "class-name", {
            "template-string": {
                pattern: /`(?:\\`|\\?[^`])*`/,
                inside: { interpolation: { pattern: /\$\{[^}]+\}/, inside: { "interpolation-punctuation": { pattern: /^\$\{|\}$/, alias: "punctuation" }, rest: c.languages.javascript } }, string: /[\s\S]+/ },
            },
        }),
    c.languages.markup &&
    c.languages.insertBefore("markup", "tag", {
        script: { pattern: /<script[\w\W]*?>[\w\W]*?<\/script>/i, inside: { tag: { pattern: /<script[\w\W]*?>|<\/script>/i, inside: c.languages.markup.tag.inside }, rest: c.languages.javascript }, alias: "language-javascript" },
    }),
        (c.languages.js = c.languages.javascript),
        (c.languages.c = c.languages.extend("clike", {
            keyword: /\b(asm|typeof|inline|auto|break|case|char|const|continue|default|do|double|else|enum|extern|float|for|goto|if|int|long|register|return|short|signed|sizeof|static|struct|switch|typedef|union|unsigned|void|volatile|while)\b/,
            operator: /\-[>-]?|\+\+?|!=?|<<?=?|>>?=?|==?|&&?|\|?\||[~^%?*\/]/,
            number: /\b-?(?:0x[\da-f]+|\d*\.?\d+(?:e[+-]?\d+)?)[ful]*\b/i,
        })),
        c.languages.insertBefore("c", "string", {
            macro: { pattern: /(^\s*)#\s*[a-z]+([^\r\n\\]|\\.|\\(?:\r\n?|\n))*/im, lookbehind: !0, alias: "property", inside: { string: { pattern: /(#\s*include\s*)(<.+?>|("|')(\\?.)+?\3)/, lookbehind: !0 } } },
        }),
        delete c.languages.c["class-name"],
        delete c.languages.c["boolean"],
        (c.languages.csharp = c.languages.extend("clike", {
            keyword: /\b(abstract|as|async|await|base|bool|break|byte|case|catch|char|checked|class|const|continue|decimal|default|delegate|do|double|else|enum|event|explicit|extern|false|finally|fixed|float|for|foreach|goto|if|implicit|in|int|interface|internal|is|lock|long|namespace|new|null|object|operator|out|override|params|private|protected|public|readonly|ref|return|sbyte|sealed|short|sizeof|stackalloc|static|string|struct|switch|this|throw|true|try|typeof|uint|ulong|unchecked|unsafe|ushort|using|virtual|void|volatile|while|add|alias|ascending|async|await|descending|dynamic|from|get|global|group|into|join|let|orderby|partial|remove|select|set|value|var|where|yield)\b/,
            string: [/@("|')(\1\1|\\\1|\\?(?!\1)[\s\S])*\1/, /("|')(\\?.)*?\1/],
            number: /\b-?(0x[\da-f]+|\d*\.?\d+)\b/i,
        })),
        c.languages.insertBefore("csharp", "keyword", { preprocessor: { pattern: /(^\s*)#.*/m, lookbehind: !0 } }),
        (c.languages.cpp = c.languages.extend("c", {
            keyword: /\b(alignas|alignof|asm|auto|bool|break|case|catch|char|char16_t|char32_t|class|compl|const|constexpr|const_cast|continue|decltype|default|delete|do|double|dynamic_cast|else|enum|explicit|export|extern|float|for|friend|goto|if|inline|int|long|mutable|namespace|new|noexcept|nullptr|operator|private|protected|public|register|reinterpret_cast|return|short|signed|sizeof|static|static_assert|static_cast|struct|switch|template|this|thread_local|throw|try|typedef|typeid|typename|union|unsigned|using|virtual|void|volatile|wchar_t|while)\b/,
            boolean: /\b(true|false)\b/,
            operator: /[-+]{1,2}|!=?|<{1,2}=?|>{1,2}=?|\->|:{1,2}|={1,2}|\^|~|%|&{1,2}|\|?\||\?|\*|\/|\b(and|and_eq|bitand|bitor|not|not_eq|or|or_eq|xor|xor_eq)\b/,
        })),
        c.languages.insertBefore("cpp", "keyword", { "class-name": { pattern: /(class\s+)[a-z0-9_]+/i, lookbehind: !0 } }),
        (c.languages.java = c.languages.extend("clike", {
            keyword: /\b(abstract|continue|for|new|switch|assert|default|goto|package|synchronized|boolean|do|if|private|this|break|double|implements|protected|throw|byte|else|import|public|throws|case|enum|instanceof|return|transient|catch|extends|int|short|try|char|final|interface|static|void|class|finally|long|strictfp|volatile|const|float|native|super|while)\b/,
            number: /\b0b[01]+\b|\b0x[\da-f]*\.?[\da-fp\-]+\b|\b\d*\.?\d+(?:e[+-]?\d+)?[df]?\b/i,
            operator: { pattern: /(^|[^.])(?:\+[+=]?|-[-=]?|!=?|<<?=?|>>?>?=?|==?|&[&=]?|\|[|=]?|\*=?|\/=?|%=?|\^=?|[?:~])/m, lookbehind: !0 },
        })),
        (c.languages.php = c.languages.extend("clike", {
            keyword: /\b(and|or|xor|array|as|break|case|cfunction|class|const|continue|declare|default|die|do|else|elseif|enddeclare|endfor|endforeach|endif|endswitch|endwhile|extends|for|foreach|function|include|include_once|global|if|new|return|static|switch|use|require|require_once|var|while|abstract|interface|public|implements|private|protected|parent|throw|null|echo|print|trait|namespace|final|yield|goto|instanceof|finally|try|catch)\b/i,
            constant: /\b[A-Z0-9_]{2,}\b/,
            comment: { pattern: /(^|[^\\])(?:\/\*[\w\W]*?\*\/|\/\/.*)/, lookbehind: !0 },
        })),
        c.languages.insertBefore("php", "class-name", { "shell-comment": { pattern: /(^|[^\\])#.*/, lookbehind: !0, alias: "comment" } }),
        c.languages.insertBefore("php", "keyword", { delimiter: /\?>|<\?(?:php)?/i, variable: /\$\w+\b/i, package: { pattern: /(\\|namespace\s+|use\s+)[\w\\]+/, lookbehind: !0, inside: { punctuation: /\\/ } } }),
        c.languages.insertBefore("php", "operator", { property: { pattern: /(->)[\w]+/, lookbehind: !0 } }),
    c.languages.markup &&
    (c.hooks.add("before-highlight", function (t) {
        "php" === t.language &&
        ((t.tokenStack = []),
            (t.backupCode = t.code),
            (t.code = t.code.replace(/(?:<\?php|<\?)[\w\W]*?(?:\?>)/gi, function (e) {
                return t.tokenStack.push(e), "{{{PHP" + t.tokenStack.length + "}}}";
            })));
    }),
        c.hooks.add("before-insert", function (e) {
            "php" === e.language && ((e.code = e.backupCode), delete e.backupCode);
        }),
        c.hooks.add("after-highlight", function (e) {
            if ("php" === e.language) {
                for (var t = 0, a = void 0; (a = e.tokenStack[t]); t++) e.highlightedCode = e.highlightedCode.replace("{{{PHP" + (t + 1) + "}}}", c.highlight(a, e.grammar, "php").replace(/\$/g, "$$$$"));
                e.element.innerHTML = e.highlightedCode;
            }
        }),
        c.hooks.add("wrap", function (e) {
            "php" === e.language && "markup" === e.type && (e.content = e.content.replace(/(\{\{\{PHP[0-9]+\}\}\})/g, '<span class="token php">$1</span>'));
        }),
        c.languages.insertBefore("php", "comment", { markup: { pattern: /<[^?]\/?(.*?)>/, inside: c.languages.markup }, php: /\{\{\{PHP[0-9]+\}\}\}/ })),
        (c.languages.python = {
            comment: { pattern: /(^|[^\\])#.*/, lookbehind: !0 },
            string: /"""[\s\S]+?"""|'''[\s\S]+?'''|("|')(?:\\?.)*?\1/,
            function: { pattern: /((?:^|\s)def[ \t]+)[a-zA-Z_][a-zA-Z0-9_]*(?=\()/g, lookbehind: !0 },
            "class-name": { pattern: /(\bclass\s+)[a-z0-9_]+/i, lookbehind: !0 },
            keyword: /\b(?:as|assert|async|await|break|class|continue|def|del|elif|else|except|exec|finally|for|from|global|if|import|in|is|lambda|pass|print|raise|return|try|while|with|yield)\b/,
            boolean: /\b(?:True|False)\b/,
            number: /\b-?(?:0[bo])?(?:(?:\d|0x[\da-f])[\da-f]*\.?\d*|\.\d+)(?:e[+-]?\d+)?j?\b/i,
            operator: /[-+%=]=?|!=|\*\*?=?|\/\/?=?|<[<=>]?|>[=>]?|[&|^~]|\b(?:or|and|not)\b/,
            punctuation: /[{}[\];(),.:]/,
        }),
        (function (e) {
            e.languages.ruby = e.languages.extend("clike", {
                comment: /#(?!\{[^\r\n]*?\}).*/,
                keyword: /\b(alias|and|BEGIN|begin|break|case|class|def|define_method|defined|do|each|else|elsif|END|end|ensure|false|for|if|in|module|new|next|nil|not|or|raise|redo|require|rescue|retry|return|self|super|then|throw|true|undef|unless|until|when|while|yield)\b/,
            });
            var t = { pattern: /#\{[^}]+\}/, inside: { delimiter: { pattern: /^#\{|\}$/, alias: "tag" }, rest: e.util.clone(e.languages.ruby) } };
            e.languages.insertBefore("ruby", "keyword", {
                regex: [
                    { pattern: /%r([^a-zA-Z0-9\s\{\(\[<])(?:[^\\]|\\[\s\S])*?\1[gim]{0,3}/, inside: { interpolation: t } },
                    { pattern: /%r\((?:[^()\\]|\\[\s\S])*\)[gim]{0,3}/, inside: { interpolation: t } },
                    { pattern: /%r\{(?:[^#{}\\]|#(?:\{[^}]+\})?|\\[\s\S])*\}[gim]{0,3}/, inside: { interpolation: t } },
                    { pattern: /%r\[(?:[^\[\]\\]|\\[\s\S])*\][gim]{0,3}/, inside: { interpolation: t } },
                    { pattern: /%r<(?:[^<>\\]|\\[\s\S])*>[gim]{0,3}/, inside: { interpolation: t } },
                    { pattern: /(^|[^/])\/(?!\/)(\[.+?]|\\.|[^/\r\n])+\/[gim]{0,3}(?=\s*($|[\r\n,.;})]))/, lookbehind: !0 },
                ],
                variable: /[@$]+[a-zA-Z_][a-zA-Z_0-9]*(?:[?!]|\b)/,
                symbol: /:[a-zA-Z_][a-zA-Z_0-9]*(?:[?!]|\b)/,
            }),
                e.languages.insertBefore("ruby", "number", {
                    builtin: /\b(Array|Bignum|Binding|Class|Continuation|Dir|Exception|FalseClass|File|Stat|File|Fixnum|Fload|Hash|Integer|IO|MatchData|Method|Module|NilClass|Numeric|Object|Proc|Range|Regexp|String|Struct|TMS|Symbol|ThreadGroup|Thread|Time|TrueClass)\b/,
                    constant: /\b[A-Z][a-zA-Z_0-9]*(?:[?!]|\b)/,
                }),
                (e.languages.ruby.string = [
                    { pattern: /%[qQiIwWxs]?([^a-zA-Z0-9\s\{\(\[<])(?:[^\\]|\\[\s\S])*?\1/, inside: { interpolation: t } },
                    { pattern: /%[qQiIwWxs]?\((?:[^()\\]|\\[\s\S])*\)/, inside: { interpolation: t } },
                    { pattern: /%[qQiIwWxs]?\{(?:[^#{}\\]|#(?:\{[^}]+\})?|\\[\s\S])*\}/, inside: { interpolation: t } },
                    { pattern: /%[qQiIwWxs]?\[(?:[^\[\]\\]|\\[\s\S])*\]/, inside: { interpolation: t } },
                    { pattern: /%[qQiIwWxs]?<(?:[^<>\\]|\\[\s\S])*>/, inside: { interpolation: t } },
                    { pattern: /("|')(#\{[^}]+\}|\\(?:\r?\n|\r)|\\?.)*?\1/, inside: { interpolation: t } },
                ]);
        })(c);
    var d = {
            isCodeSample: function (e) {
                return e && "PRE" === e.nodeName && -1 !== e.className.indexOf("language-");
            },
            trimArg: function (a) {
                return function (e, t) {
                    return a(t);
                };
            },
        },
        p = function (e) {
            var t = e.selection.getNode();
            return d.isCodeSample(t) ? t : null;
        },
        f = p,
        h = function (t, a, n) {
            t.undoManager.transact(function () {
                var e = p(t);
                (n = i.DOM.encode(n)),
                    e
                        ? (t.dom.setAttrib(e, "class", "language-" + a), (e.innerHTML = n), c.highlightElement(e), t.selection.select(e))
                        : (t.insertContent('<pre id="__new" class="language-' + a + '">' + n + "</pre>"), t.selection.select(t.$("#__new").removeAttr("id")[0]));
            });
        },
        m = function (e) {
            var t = p(e);
            return t ? t.textContent : "";
        },
        b = function (e) {
            var t = a(e);
            return (
                t || [
                    { text: "HTML/XML", value: "markup" },
                    { text: "JavaScript", value: "javascript" },
                    { text: "CSS", value: "css" },
                    { text: "PHP", value: "php" },
                    { text: "Ruby", value: "ruby" },
                    { text: "Python", value: "python" },
                    { text: "Java", value: "java" },
                    { text: "C", value: "c" },
                    { text: "C#", value: "csharp" },
                    { text: "C++", value: "cpp" },
                ]
            );
        },
        y = function (e) {
            var t,
                a = f(e);
            return a && (t = a.className.match(/language-(\w+)/)) ? t[1] : "";
        },
        v = function (t) {
            var e = o(t),
                a = l(t),
                n = y(t),
                i = b(t),
                r = m(t);
            t.windowManager.open({
                title: "Insert/Edit code sample",
                minWidth: e,
                minHeight: a,
                layout: "flex",
                direction: "column",
                align: "stretch",
                body: [
                    { type: "listbox", name: "language", label: "Language", maxWidth: 200, value: n, values: i },
                    { type: "textbox", name: "code", multiline: !0, spellcheck: !1, ariaLabel: "Code view", flex: 1, style: "direction: ltr; text-align: left", classes: "monospace", value: r, autofocus: !0 },
                ],
                onSubmit: function (e) {
                    h(t, e.data.language, e.data.code);
                },
            });
        },
        k = function (t) {
            t.addCommand("codesample", function () {
                var e = t.selection.getNode();
                t.selection.isCollapsed() || d.isCodeSample(e) ? v(t) : t.formatter.toggle("code");
            });
        },
        w = function (a) {
            var i = a.$;
            a.on("PreProcess", function (e) {
                i("pre[contenteditable=false]", e.node)
                    .filter(d.trimArg(d.isCodeSample))
                    .each(function (e, t) {
                        var a = i(t),
                            n = t.textContent;
                        a.attr("class", i.trim(a.attr("class"))),
                            a.removeAttr("contentEditable"),
                            a.empty().append(
                                i("<code></code>").each(function () {
                                    this.textContent = n;
                                })
                            );
                    });
            }),
                a.on("SetContent", function () {
                    var e = i("pre")
                        .filter(d.trimArg(d.isCodeSample))
                        .filter(function (e, t) {
                            return "false" !== t.contentEditable;
                        });
                    e.length &&
                    a.undoManager.transact(function () {
                        e.each(function (e, t) {
                            i(t)
                                .find("br")
                                .each(function (e, t) {
                                    t.parentNode.replaceChild(a.getDoc().createTextNode("\n"), t);
                                }),
                                (t.contentEditable = !1),
                                (t.innerHTML = a.dom.encode(t.textContent)),
                                c.highlightElement(t),
                                (t.className = i.trim(t.className));
                        });
                    });
                });
        },
        x = function (e, t, a, n) {
            var i,
                r = s(e);
            (e.inline && a.get()) ||
            (!e.inline && n.get()) ||
            (e.inline ? a.set(!0) : n.set(!0), !1 !== r && ((i = e.dom.create("link", { rel: "stylesheet", href: r || t + "/css/prism.css" })), e.getDoc().getElementsByTagName("head")[0].appendChild(i)));
        },
        S = function (e) {
            e.addButton("codesample", { cmd: "codesample", title: "Insert/Edit code sample" }), e.addMenuItem("codesample", { cmd: "codesample", text: "Code sample", icon: "codesample" });
        },
        C = n(!1);
    e.add("codesample", function (t, e) {
        var a = n(!1);
        w(t),
            S(t),
            k(t),
            t.on("init", function () {
                x(t, e, C, a);
            }),
            t.on("dblclick", function (e) {
                d.isCodeSample(e.target) && v(t);
            });
    });
})(window);
