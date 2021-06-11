// PHP Linter to CodeMirror, copyright (c) by Ioan CHIRIAC
// Distributed under a BSD 3-Clause License : https://github.com/glayzzle/codemirror-linter/blob/master/LICENSE

// Note: Adding a variable to make it work with WordPress' implementation of CodeMirror
var CodeMirror = wp.CodeMirror;

(function(mod) {
  if (typeof exports == "object" && typeof module == "object") {
    mod(require("cm/lib/codemirror"), require('php-parser'));
  } else {
    mod(CodeMirror, require('php-parser'));
  }
})(function(CodeMirror, phpParser) {
  "use strict";

  var messages = [];

  /**
   * Adds a new linting message
   */
  function addMessage(msg, position, type) {
    var start, end;
    if (position.lineNumber && position.columnNumber) {
      start = CodeMirror.Pos(position.lineNumber - 1, position.columnNumber - 1);
      end = CodeMirror.Pos(position.lineNumber - 1, position.columnNumber);
    } else if (position.start && position.end) {
      if (position.end.offset < position.start.offset) {
        end = CodeMirror.Pos(position.start.line - 1, position.start.column);
        start = CodeMirror.Pos(position.end.line - 1, position.end.column);
      } else {
        start = CodeMirror.Pos(position.start.line - 1, position.start.column);
        end = CodeMirror.Pos(position.end.line - 1, position.end.column);
      }
    }
    messages.push({
      message: msg,
      severity: type,
      from: start,
      to: end
    });
  }

  function addError(msg, position) {
    addMessage(msg, position, "error");
  }

  function addWarning(msg, position) {
    addMessage(msg, position, "warning");
  }

  /**
   * Recursive ndoe visitor
   */
  function visit(node, opt) {
    if (node.hasOwnProperty('kind')) {
      validate(node, opt);
    }
    for(var k in node) {
      if (node.hasOwnProperty(k)) {
        var child = node[k];
        if (!child) continue;
        if (child.hasOwnProperty('kind')) {
          visit(child, opt);
        } else if (Array.isArray(child)) {
          child.forEach(function(item) {
            visit(item, opt);
          });
        } else if (typeof child === 'object') {
          visit(child, opt);
        }
      }
    }
  }

  /**
   * Validation function
   */
  function validate(node, opt) {
    if (opt.disableEval) {
      if (node.kind === 'eval') {
        return addWarning('Eval is evil', node.loc);
      }
      if (node.kind === 'call' && node.what.name === 'create_function') {
        return addWarning('Eval is evil', node.loc);
      }
    }
    if (node.kind === 'exit' && opt.disableExit) {
      return addWarning('You should not use exit or die', node.loc);
    }
    if (opt.disablePHP7) {
      if (node.kind === 'array' && node.shortForm) {
        return addWarning('PHP 7 feature disabled', node.loc);
      }
      // ... todo
    }
    if (opt.disabledFunctions) {
      if (node.kind === 'call' && opt.disabledFunctions.indexOf(node.what.name) > -1) {
        return addError('Function "' + node.what.name + '" is not available', node.what.loc);
      }
    }
    if (opt.deprecatedFunctions) {
      if (node.kind === 'call' && opt.deprecatedFunctions.indexOf(node.what.name) > -1) {
        return addWarning('Function "' + node.what.name + '" is deprecated', node.what.loc);
      }
    }
  }

  // validate some code
  CodeMirror.registerHelper("lint", "php", function phpLint(text, options) {
    messages = [];
    if (phpParser) {
      try {
        var ast = phpParser.parseCode(text, {
          parser: {
            suppressErrors: true
          },
          ast: {
            withPositions: true
          }
        });
        // check errors
        if (ast.errors && ast.errors.length > 0) {
          for(var i = 0; i < ast.errors.length; i++) {
            addError(ast.errors[i].message, ast.errors[i].loc);
          }
        }
        // filter nodes
        visit(ast, options);
      } catch(e) {
        addError(e.message, e);
      }
    } else if (window.console) {
      window.console.error("Error: php-parser not defined, CodeMirror PHP linting cannot run.");
    }
    return messages;
  });

});
