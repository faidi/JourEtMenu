(function() {
'use strict';

var Faye = {
  VERSION:          '1.1.1',

  BAYEUX_VERSION:   '1.0',
  ID_LENGTH:        160,
  JSONP_CALLBACK:   'jsonpcallback',
  CONNECTION_TYPES: ['long-polling', 'cross-origin-long-polling', 'callback-polling', 'websocket', 'eventsource', 'in-process'],

  MANDATORY_CONNECTION_TYPES: ['long-polling', 'callback-polling', 'in-process'],

  ENV: (typeof window !== 'undefined') ? window : global,

  extend: function(dest, source, overwrite) {
    if (!source) return dest;
    for (var key in source) {
      if (!source.hasOwnProperty(key)) continue;
      if (dest.hasOwnProperty(key) && overwrite === false) continue;
      if (dest[key] !== source[key])
        dest[key] = source[key];
    }
    return dest;
  },

  random: function(bitlength) {
    bitlength = bitlength || this.ID_LENGTH;
    var maxLength = Math.ceil(bitlength * Math.log(2) / Math.log(36));
    var string = csprng(bitlength, 36);
    while (string.length < maxLength) string = '0' + string;
    return string;
  },

  validateOptions: function(options, validKeys) {
    for (var key in options) {
      if (this.indexOf(validKeys, key) < 0)
        throw new Error('Unrecognized option: ' + key);
    }
  },

  clientIdFromMessages: function(messages) {
    var connect = this.filter([].concat(messages), function(message) {
      return message.channel === '/meta/connect';
    });
    return connect[0] && connect[0].clientId;
  },

  copyObject: function(object) {
    var clone, i, key;
    if (object instanceof Array) {
      clone = [];
      i = object.length;
      while (i--) clone[i] = Faye.copyObject(object[i]);
      return clone;
    } else if (typeof object === 'object') {
      clone = (object === null) ? null : {};
      for (key in object) clone[key] = Faye.copyObject(object[key]);
      return clone;
    } else {
      return object;
    }
  },

  commonElement: function(lista, listb) {
    for (var i = 0, n = lista.length; i < n; i++) {
      if (this.indexOf(listb, lista[i]) !== -1)
        return lista[i];
    }
    return null;
  },

  indexOf: function(list, needle) {
    if (list.indexOf) return list.indexOf(needle);

    for (var i = 0, n = list.length; i < n; i++) {
      if (list[i] === needle) return i;
    }
    return -1;
  },

  map: function(object, callback, context) {
    if (object.map) return object.map(callback, context);
    var result = [];

    if (object instanceof Array) {
      for (var i = 0, n = object.length; i < n; i++) {
        result.push(callback.call(context || null, object[i], i));
      }
    } else {
      for (var key in object) {
        if (!object.hasOwnProperty(key)) continue;
        result.push(callback.call(context || null, key, object[key]));
      }
    }
    return result;
  },

  filter: function(array, callback, context) {
    if (array.filter) return array.filter(callback, context);
    var result = [];
    for (var i = 0, n = array.length; i < n; i++) {
      if (callback.call(context || null, array[i], i))
        result.push(array[i]);
    }
    return result;
  },

  asyncEach: function(list, iterator, callback, context) {
    var n       = list.length,
        i       = -1,
        calls   = 0,
        looping = false;

    var iterate = function() {
      calls -= 1;
      i += 1;
      if (i === n) return callback && callback.call(context);
      iterator(list[i], resume);
    };

    var loop = function() {
      if (looping) return;
      looping = true;
      while (calls > 0) iterate();
      looping = false;
    };

    var resume = function() {
      calls += 1;
      loop();
    };
    resume();
  },

  // http://assanka.net/content/tech/2009/09/02/json2-js-vs-prototype/
  toJSON: function(object) {
    if (!this.stringify) return JSON.stringify(object);

    return this.stringify(object, function(key, value) {
      return (this[key] instanceof Array) ? this[key] : value;
    });
  }
};

if (typeof module !== 'undefined')
  module.exports = Faye;
else if (typeof window !== 'undefined')
  window.Faye = Faye;

Faye.Class = function(parent, methods) {
  if (typeof parent !== 'function') {
    methods = parent;
    parent  = Object;
  }

  var klass = function() {
    if (!this.initialize) return this;
    return this.initialize.apply(this, arguments) || this;
  };

  var bridge = function() {};
  bridge.prototype = parent.prototype;

  klass.prototype = new bridge();
  Faye.extend(klass.prototype, methods);

  return klass;
};

(function() {
var EventEmitter = Faye.EventEmitter = function() {};

/*
Copyright Joyent, Inc. and other Node contributors. All rights reserved.
Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

var isArray = typeof Array.isArray === 'function'
    ? Array.isArray
    : function (xs) {
        return Object.prototype.toString.call(xs) === '[object Array]'
    }
;
function indexOf (xs, x) {
    if (xs.indexOf) return xs.indexOf(x);
    for (var i = 0; i < xs.length; i++) {
        if (x === xs[i]) return i;
    }
    return -1;
}


EventEmitter.prototype.emit = function(type) {
  // If there is no 'error' event listener then throw.
  if (type === 'error') {
    if (!this._events || !this._events.error ||
        (isArray(this._events.error) && !this._events.error.length))
    {
      if (arguments[1] instanceof Error) {
        throw arguments[1]; // Unhandled 'error' event
      } else {
        throw new Error("Uncaught, unspecified 'error' event.");
      }
      return false;
    }
  }

  if (!this._events) return false;
  var handler = this._events[type];
  if (!handler) return false;

  if (typeof handler == 'function') {
    switch (arguments.length) {
      // fast cases
      case 1:
        handler.call(this);
        break;
      case 2:
        handler.call(this, arguments[1]);
        break;
      case 3:
        handler.call(this, arguments[1], arguments[2]);
        break;
      // slower
      default:
        var args = Array.prototype.slice.call(arguments, 1);
        handler.apply(this, args);
    }
    return true;

  } else if (isArray(handler)) {
    var args = Array.prototype.slice.call(arguments, 1);

    var listeners = handler.slice();
    for (var i = 0, l = listeners.length; i < l; i++) {
      listeners[i].apply(this, args);
    }
    return true;

  } else {
    return false;
  }
};

// EventEmitter is defined in src/node_events.cc
// EventEmitter.prototype.emit() is also defined there.
EventEmitter.prototype.addListener = function(type, listener) {
  if ('function' !== typeof listener) {
    throw new Error('addListener only takes instances of Function');
  }

  if (!this._events) this._events = {};

  // To avoid recursion in the case that type == "newListeners"! Before
  // adding it to the listeners, first emit "newListeners".
  this.emit('newListener', type, listener);

  if (!this._events[type]) {
    // Optimize the case of one listener. Don't need the extra array object.
    this._events[type] = listener;
  } else if (isArray(this._events[type])) {
    // If we've already got an array, just append.
    this._events[type].push(listener);
  } else {
    // Adding the second element, need to change to array.
    this._events[type] = [this._events[type], listener];
  }

  return this;
};

EventEmitter.prototype.on = EventEmitter.prototype.addListener;

EventEmitter.prototype.once = function(type, listener) {
  var self = this;
  self.on(type, function g() {
    self.removeListener(type, g);
    listener.apply(this, arguments);
  });

  return this;
};

EventEmitter.prototype.removeListener = function(type, listener) {
  if ('function' !== typeof listener) {
    throw new Error('removeListener only takes instances of Function');
  }

  // does not use listeners(), so no side effect of creating _events[type]
  if (!this._events || !this._events[type]) return this;

  var list = this._events[type];

  if (isArray(list)) {
    var i = indexOf(list, listener);
    if (i < 0) return this;
    list.splice(i, 1);
    if (list.length == 0)
      delete this._events[type];
  } else if (this._events[type] === listener) {
    delete this._events[type];
  }

  return this;
};

EventEmitter.prototype.removeAllListeners = function(type) {
  if (arguments.length === 0) {
    this._events = {};
    return this;
  }

  // does not use listeners(), so no side effect of creating _events[type]
  if (type && this._events && this._events[type]) this._events[type] = null;
  return this;
};

EventEmitter.prototype.listeners = function(type) {
  if (!this._events) this._events = {};
  if (!this._events[type]) this._events[type] = [];
  if (!isArray(this._events[type])) {
    this._events[type] = [this._events[type]];
  }
  return this._events[type];
};

})();

Faye.Namespace = Faye.Class({
  initialize: function() {
    this._used = {};
  },

  exists: function(id) {
    return this._used.hasOwnProperty(id);
  },

  generate: function() {
    var name = Faye.random();
    while (this._used.hasOwnProperty(name))
      name = Faye.random();
    return this._used[name] = name;
  },

  release: function(id) {
    delete this._used[id];
  }
});

(function() {
'use strict';

var timeout = setTimeout, defer;

if (typeof setImmediate === 'function')
  defer = function(fn) { setImmediate(fn) };
else if (typeof process === 'object' && process.nextTick)
  defer = function(fn) { process.nextTick(fn) };
else
  defer = function(fn) { timeout(fn, 0) };

var PENDING   = 0,
    FULFILLED = 1,
    REJECTED  = 2;

var RETURN = function(x) { return x },
    THROW  = function(x) { throw  x };

var Promise = function(task) {
  this._state       = PENDING;
  this._onFulfilled = [];
  this._onRejected  = [];

  if (typeof task !== 'function') return;
  var self = this;

  task(function(value)  { fulfill(self, value) },
       function(reason) { reject(self, reason) });
};

Promise.prototype.then = function(onFulfilled, onRejected) {
  var next = new Promise();
  registerOnFulfilled(this, onFulfilled, next);
  registerOnRejected(this, onRejected, next);
  return next;
};

var registerOnFulfilled = function(promise, onFulfilled, next) {
  if (typeof onFulfilled !== 'function') onFulfilled = RETURN;
  var handler = function(value) { invoke(onFulfilled, value, next) };

  if (promise._state === PENDING) {
    promise._onFulfilled.push(handler);
  } else if (promise._state === FULFILLED) {
    handler(promise._value);
  }
};

var registerOnRejected = function(promise, onRejected, next) {
  if (typeof onRejected !== 'function') onRejected = THROW;
  var handler = function(reason) { invoke(onRejected, reason, next) };

  if (promise._state === PENDING) {
    promise._onRejected.push(handler);
  } else if (promise._state === REJECTED) {
    handler(promise._reason);
  }
};

var invoke = function(fn, value, next) {
  defer(function() { _invoke(fn, value, next) });
};

var _invoke = function(fn, value, next) {
  var outcome;

  try {
    outcome = fn(value);
  } catch (error) {
    return reject(next, error);
  }

  if (outcome === next) {
    reject(next, new TypeError('Recursive promise chain detected'));
  } else {
    fulfill(next, outcome);
  }
};

var fulfill = Promise.fulfill = Promise.resolve = function(promise, value) {
  var called = false, type, then;

  try {
    type = typeof value;
    then = value !== null && (type === 'function' || type === 'object') && value.then;

    if (typeof then !== 'function') return _fulfill(promise, value);

    then.call(value, function(v) {
      if (!(called ^ (called = true))) return;
      fulfill(promise, v);
    }, function(r) {
      if (!(called ^ (called = true))) return;
      reject(promise, r);
    });
  } catch (error) {
    if (!(called ^ (called = true))) return;
    reject(promise, error);
  }
};

var _fulfill = function(promise, value) {
  if (promise._state !== PENDING) return;

  promise._state      = FULFILLED;
  promise._value      = value;
  promise._onRejected = [];

  var onFulfilled = promise._onFulfilled, fn;
  while (fn = onFulfilled.shift()) fn(value);
};

var reject = Promise.reject = function(promise, reason) {
  if (promise._state !== PENDING) return;

  promise._state       = REJECTED;
  promise._reason      = reason;
  promise._onFulfilled = [];

  var onRejected = promise._onRejected, fn;
  while (fn = onRejected.shift()) fn(reason);
};

Promise.all = function(promises) {
  return new Promise(function(fulfill, reject) {
    var list = [],
         n   = promises.length,
         i;

    if (n === 0) return fulfill(list);

    for (i = 0; i < n; i++) (function(promise, i) {
      Promise.fulfilled(promise).then(function(value) {
        list[i] = value;
        if (--n === 0) fulfill(list);
      }, reject);
    })(promises[i], i);
  });
};

Promise.defer = defer;

Promise.deferred = Promise.pending = function() {
  var tuple = {};

  tuple.promise = new Promise(function(fulfill, reject) {
    tuple.fulfill = tuple.resolve = fulfill;
    tuple.reject  = reject;
  });
  return tuple;
};

Promise.fulfilled = Promise.resolved = function(value) {
  return new Promise(function(fulfill, reject) { fulfill(value) });
};

Promise.rejected = function(reason) {
  return new Promise(function(fulfill, reject) { reject(reason) });
};

if (typeof Faye === 'undefined')
  module.exports = Promise;
else
  Faye.Promise = Promise;

})();

Faye.Set = Faye.Class({
  initialize: function() {
    this._index = {};
  },

  add: function(item) {
    var key = (item.id !== undefined) ? item.id : item;
    if (this._index.hasOwnProperty(key)) return false;
    this._index[key] = item;
    return true;
  },

  forEach: function(block, context) {
    for (var key in this._index) {
      if (this._index.hasOwnProperty(key))
        block.call(context, this._index[key]);
    }
  },

  isEmpty: function() {
    for (var key in this._index) {
      if (this._index.hasOwnProperty(key)) return false;
    }
    return true;
  },

  member: function(item) {
    for (var key in this._index) {
      if (this._index[key] === item) return true;
    }
    return false;
  },

  remove: function(item) {
    var key = (item.id !== undefined) ? item.id : item;
    var removed = this._index[key];
    delete this._index[key];
    return removed;
  },

  toArray: function() {
    var array = [];
    this.forEach(function(item) { array.push(item) });
    return array;
  }
});

Faye.URI = {
  isURI: function(uri) {
    return uri && uri.protocol && uri.host && uri.path;
  },

  isSameOrigin: function(uri) {
    var location = Faye.ENV.location;
    return uri.protocol === location.protocol &&
           uri.hostname === location.hostname &&
           uri.port     === location.port;
  },

  parse: function(url) {
    if (typeof url !== 'string') return url;
    var uri = {}, parts, query, pairs, i, n, data;

    var consume = function(name, pattern) {
      url = url.replace(pattern, function(match) {
        uri[name] = match;
        return '';
      });
      uri[name] = uri[name] || '';
    };

    consume('protocol', /^[a-z]+\:/i);
    consume('host',     /^\/\/[^\/\?#]+/);

    if (!/^\//.test(url) && !uri.host)
      url = Faye.ENV.location.pathname.replace(/[^\/]*$/, '') + url;

    consume('pathname', /^[^\?#]*/);
    consume('search',   /^\?[^#]*/);
    consume('hash',     /^#.*/);

    uri.protocol = uri.protocol || Faye.ENV.location.protocol;

    if (uri.host) {
      uri.host     = uri.host.substr(2);
      parts        = uri.host.split(':');
      uri.hostname = parts[0];
      uri.port     = parts[1] || '';
    } else {
      uri.host     = Faye.ENV.location.host;
      uri.hostname = Faye.ENV.location.hostname;
      uri.port     = Faye.ENV.location.port;
    }

    uri.pathname = uri.pathname || '/';
    uri.path = uri.pathname + uri.search;

    query = uri.search.replace(/^\?/, '');
    pairs = query ? query.split('&') : [];
    data  = {};

    for (i = 0, n = pairs.length; i < n; i++) {
      parts = pairs[i].split('=');
      data[decodeURIComponent(parts[0] || '')] = decodeURIComponent(parts[1] || '');
    }

    uri.query = data;

    uri.href = this.stringify(uri);
    return uri;
  },

  stringify: function(uri) {
    var string = uri.protocol + '//' + uri.hostname;
    if (uri.port) string += ':' + uri.port;
    string += uri.pathname + this.queryString(uri.query) + (uri.hash || '');
    return string;
  },

  queryString: function(query) {
    var pairs = [];
    for (var key in query) {
      if (!query.hasOwnProperty(key)) continue;
      pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(query[key]));
    }
    if (pairs.length === 0) return '';
    return '?' + pairs.join('&');
  }
};

Faye.Error = Faye.Class({
  initialize: function(code, params, message) {
    this.code    = code;
    this.params  = Array.prototype.slice.call(params);
    this.message = message;
  },

  toString: function() {
    return this.code + ':' +
           this.params.join(',') + ':' +
           this.message;
  }
});

Faye.Error.parse = function(message) {
  message = message || '';
  if (!Faye.Grammar.ERROR.test(message)) return new this(null, [], message);

  var parts   = message.split(':'),
      code    = parseInt(parts[0]),
      params  = parts[1].split(','),
      message = parts[2];

  return new this(code, params, message);
};




Faye.Error.versionMismatch = function() {
  return new this(300, arguments, 'Version mismatch').toString();
};

Faye.Error.conntypeMismatch = function() {
  return new this(301, arguments, 'Connection types not supported').toString();
};

Faye.Error.extMismatch = function() {
  return new this(302, arguments, 'Extension mismatch').toString();
};

Faye.Error.badRequest = function() {
  return new this(400, arguments, 'Bad request').toString();
};

Faye.Error.clientUnknown = function() {
  return new this(401, arguments, 'Unknown client').toString();
};

Faye.Error.parameterMissing = function() {
  return new this(402, arguments, 'Missing required parameter').toString();
};

Faye.Error.channelForbidden = function() {
  return new this(403, arguments, 'Forbidden channel').toString();
};

Faye.Error.channelUnknown = function() {
  return new this(404, arguments, 'Unknown channel').toString();
};

Faye.Error.channelInvalid = function() {
  return new this(405, arguments, 'Invalid channel').toString();
};

Faye.Error.extUnknown = function() {
  return new this(406, arguments, 'Unknown extension').toString();
};

Faye.Error.publishFailed = function() {
  return new this(407, arguments, 'Failed to publish').toString();
};

Faye.Error.serverError = function() {
  return new this(500, arguments, 'Internal server error').toString();
};


Faye.Deferrable = {
  then: function(callback, errback) {
    var self = this;
    if (!this._promise)
      this._promise = new Faye.Promise(function(fulfill, reject) {
        self._fulfill = fulfill;
        self._reject  = reject;
      });

    if (arguments.length === 0)
      return this._promise;
    else
      return this._promise.then(callback, errback);
  },

  callback: function(callback, context) {
    return this.then(function(value) { callback.call(context, value) });
  },

  errback: function(callback, context) {
    return this.then(null, function(reason) { callback.call(context, reason) });
  },

  timeout: function(seconds, message) {
    this.then();
    var self = this;
    this._timer = Faye.ENV.setTimeout(function() {
      self._reject(message);
    }, seconds * 1000);
  },

  setDeferredStatus: function(status, value) {
    if (this._timer) Faye.ENV.clearTimeout(this._timer);

    this.then();

    if (status === 'succeeded')
      this._fulfill(value);
    else if (status === 'failed')
      this._reject(value);
    else
      delete this._promise;
  }
};

Faye.Publisher = {
  countListeners: function(eventType) {
    return this.listeners(eventType).length;
  },

  bind: function(eventType, listener, context) {
    var slice   = Array.prototype.slice,
        handler = function() { listener.apply(context, slice.call(arguments)) };

    this._listeners = this._listeners || [];
    this._listeners.push([eventType, listener, context, handler]);
    return this.on(eventType, handler);
  },

  unbind: function(eventType, listener, context) {
    this._listeners = this._listeners || [];
    var n = this._listeners.length, tuple;

    while (n--) {
      tuple = this._listeners[n];
      if (tuple[0] !== eventType) continue;
      if (listener && (tuple[1] !== listener || tuple[2] !== context)) continue;
      this._listeners.splice(n, 1);
      this.removeListener(eventType, tuple[3]);
    }
  }
};

Faye.extend(Faye.Publisher, Faye.EventEmitter.prototype);
Faye.Publisher.trigger = Faye.Publisher.emit;

Faye.Timeouts = {
  addTimeout: function(name, delay, callback, context) {
    this._timeouts = this._timeouts || {};
    if (this._timeouts.hasOwnProperty(name)) return;
    var self = this;
    this._timeouts[name] = Faye.ENV.setTimeout(function() {
      delete self._timeouts[name];
      callback.call(context);
    }, 1000 * delay);
  },

  removeTimeout: function(name) {
    this._timeouts = this._timeouts || {};
    var timeout = this._timeouts[name];
    if (!timeout) return;
    Faye.ENV.clearTimeout(timeout);
    delete this._timeouts[name];
  },

  removeAllTimeouts: function() {
    this._timeouts = this._timeouts || {};
    for (var name in this._timeouts) this.removeTimeout(name);
  }
};

Faye.Logging = {
  LOG_LEVELS: {
    fatal:  4,
    error:  3,
    warn:   2,
    info:   1,
    debug:  0
  },

  writeLog: function(messageArgs, level) {
    if (!Faye.logger) return;

    var args   = Array.prototype.slice.apply(messageArgs),
        banner = '[Faye',
        klass  = this.className,

        message = args.shift().replace(/\?/g, function() {
          try {
            return Faye.toJSON(args.shift());
          } catch (e) {
            return '[Object]';
          }
        });

    for (var key in Faye) {
      if (klass) continue;
      if (typeof Faye[key] !== 'function') continue;
      if (this instanceof Faye[key]) klass = key;
    }
    if (klass) banner += '.' + klass;
    banner += '] ';

    if (typeof Faye.logger[level] === 'function')
      Faye.logger[level](banner + message);
    else if (typeof Faye.logger === 'function')
      Faye.logger(banner + message);
  }
};

(function() {
  for (var key in Faye.Logging.LOG_LEVELS)
    (function(level) {
      Faye.Logging[level] = function() {
        this.writeLog(arguments, level);
      };
    })(key);
})();

Faye.Grammar = {
  CHANNEL_NAME:     /^\/(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)))+(\/(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)))+)*$/,
  CHANNEL_PATTERN:  /^(\/(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)))+)*\/\*{1,2}$/,
  ERROR:            /^([0-9][0-9][0-9]:(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)| |\/|\*|\.))*(,(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)| |\/|\*|\.))*)*:(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)| |\/|\*|\.))*|[0-9][0-9][0-9]::(((([a-z]|[A-Z])|[0-9])|(\-|\_|\!|\~|\(|\)|\$|\@)| |\/|\*|\.))*)$/,
  VERSION:          /^([0-9])+(\.(([a-z]|[A-Z])|[0-9])(((([a-z]|[A-Z])|[0-9])|\-|\_))*)*$/
};

Faye.Extensible = {
  addExtension: function(extension) {
    this._extensions = this._extensions || [];
    this._extensions.push(extension);
    if (extension.added) extension.added(this);
  },

  removeExtension: function(extension) {
    if (!this._extensions) return;
    var i = this._extensions.length;
    while (i--) {
      if (this._extensions[i] !== extension) continue;
      this._extensions.splice(i,1);
      if (extension.removed) extension.removed(this);
    }
  },

  pipeThroughExtensions: function(stage, message, request, callback, context) {
    this.debug('Passing through ? extensions: ?', stage, message);

    if (!this._extensions) return callback.call(context, message);
    var extensions = this._extensions.slice();

    var pipe = function(message) {
      if (!message) return callback.call(context, message);

      var extension = extensions.shift();
      if (!extension) return callback.call(context, message);

      var fn = extension[stage];
      if (!fn) return pipe(message);

      if (fn.length >= 3) extension[stage](message, request, pipe);
      else                extension[stage](message, pipe);
    };
    pipe(message);
  }
};

Faye.extend(Faye.Extensible, Faye.Logging);

Faye.Channel = Faye.Class({
  initialize: function(name) {
    this.id = this.name = name;
  },

  push: function(message) {
    this.trigger('message', message);
  },

  isUnused: function() {
    return this.countListeners('message') === 0;
  }
});

Faye.extend(Faye.Channel.prototype, Faye.Publisher);

Faye.extend(Faye.Channel, {
  HANDSHAKE:    '/meta/handshake',
  CONNECT:      '/meta/connect',
  SUBSCRIBE:    '/meta/subscribe',
  UNSUBSCRIBE:  '/meta/unsubscribe',
  DISCONNECT:   '/meta/disconnect',

  META:         'meta',
  SERVICE:      'service',

  expand: function(name) {
    var segments = this.parse(name),
        channels = ['/**', name];

    var copy = segments.slice();
    copy[copy.length - 1] = '*';
    channels.push(this.unparse(copy));

    for (var i = 1, n = segments.length; i < n; i++) {
      copy = segments.slice(0, i);
      copy.push('**');
      channels.push(this.unparse(copy));
    }

    return channels;
  },

  isValid: function(name) {
    return Faye.Grammar.CHANNEL_NAME.test(name) ||
           Faye.Grammar.CHANNEL_PATTERN.test(name);
  },

  parse: function(name) {
    if (!this.isValid(name)) return null;
    return name.split('/').slice(1);
  },

  unparse: function(segments) {
    return '/' + segments.join('/');
  },

  isMeta: function(name) {
    var segments = this.parse(name);
    return segments ? (segments[0] === this.META) : null;
  },

  isService: function(name) {
    var segments = this.parse(name);
    return segments ? (segments[0] === this.SERVICE) : null;
  },

  isSubscribable: function(name) {
    if (!this.isValid(name)) return null;
    return !this.isMeta(name) && !this.isService(name);
  },

  Set: Faye.Class({
    initialize: function() {
      this._channels = {};
    },

    getKeys: function() {
      var keys = [];
      for (var key in this._channels) keys.push(key);
      return keys;
    },

    remove: function(name) {
      delete this._channels[name];
    },

    hasSubscription: function(name) {
      return this._channels.hasOwnProperty(name);
    },

    subscribe: function(names, callback, context) {
      var name;
      for (var i = 0, n = names.length; i < n; i++) {
        name = names[i];
        var channel = this._channels[name] = this._channels[name] || new Faye.Channel(name);
        if (callback) channel.bind('message', callback, context);
      }
    },

    unsubscribe: function(name, callback, context) {
      var channel = this._channels[name];
      if (!channel) return false;
      channel.unbind('message', callback, context);

      if (channel.isUnused()) {
        this.remove(name);
        return true;
      } else {
        return false;
      }
    },

    distributeMessage: function(message) {
      var channels = Faye.Channel.expand(message.channel);

      for (var i = 0, n = channels.length; i < n; i++) {
        var channel = this._channels[channels[i]];
        if (channel) channel.trigger('message', message.data);
      }
    }
  })
});

Faye.Publication = Faye.Class(Faye.Deferrable);

Faye.Subscription = Faye.Class({
  initialize: function(client, channels, callback, context) {
    this._client    = client;
    this._channels  = channels;
    this._callback  = callback;
    this._context     = context;
    this._cancelled = false;
  },

  cancel: function() {
    if (this._cancelled) return;
    this._client.unsubscribe(this._channels, this._callback, this._context);
    this._cancelled = true;
  },

  unsubscribe: function() {
    this.cancel();
  }
});

Faye.extend(Faye.Subscription.prototype, Faye.Deferrable);

Faye.Client = Faye.Class({
  UNCONNECTED:        1,
  CONNECTING:         2,
  CONNECTED:          3,
  DISCONNECTED:       4,

  HANDSHAKE:          'handshake',
  RETRY:              'retry',
  NONE:               'none',

  CONNECTION_TIMEOUT: 60,

  DEFAULT_ENDPOINT:   '/bayeux',
  INTERVAL:           0,

  initialize: function(endpoint, options) {
    this.info('New client created for ?', endpoint);
    options = options || {};

    Faye.validateOptions(options, ['interval', 'timeout', 'endpoints', 'proxy', 'retry', 'scheduler', 'websocketExtensions', 'tls', 'ca']);

    this._endpoint   = endpoint || this.DEFAULT_ENDPOINT;
    this._channels   = new Faye.Channel.Set();
    this._dispatcher = new Faye.Dispatcher(this, this._endpoint, options);

    this._messageId = 0;
    this._state     = this.UNCONNECTED;

    this._responseCallbacks = {};

    this._advice = {
      reconnect: this.RETRY,
      interval:  1000 * (options.interval || this.INTERVAL),
      timeout:   1000 * (options.timeout  || this.CONNECTION_TIMEOUT)
    };
    this._dispatcher.timeout = this._advice.timeout / 1000;

    this._dispatcher.bind('message', this._receiveMessage, this);

    if (Faye.Event && Faye.ENV.onbeforeunload !== undefined)
      Faye.Event.on(Faye.ENV, 'beforeunload', function() {
        if (Faye.indexOf(this._dispatcher._disabled, 'autodisconnect') < 0)
          this.disconnect();
      }, this);
  },

  addWebsocketExtension: function(extension) {
    return this._dispatcher.addWebsocketExtension(extension);
  },

  disable: function(feature) {
    return this._dispatcher.disable(feature);
  },

  setHeader: function(name, value) {
    return this._dispatcher.setHeader(name, value);
  },

  // Request
  // MUST include:  * channel
  //                * version
  //                * supportedConnectionTypes
  // MAY include:   * minimumVersion
  //                * ext
  //                * id
  //
  // Success Response                             Failed Response
  // MUST include:  * channel                     MUST include:  * channel
  //                * version                                    * successful
  //                * supportedConnectionTypes                   * error
  //                * clientId                    MAY include:   * supportedConnectionTypes
  //                * successful                                 * advice
  // MAY include:   * minimumVersion                             * version
  //                * advice                                     * minimumVersion
  //                * ext                                        * ext
  //                * id                                         * id
  //                * authSuccessful
  handshake: function(callback, context) {
    if (this._advice.reconnect === this.NONE) return;
    if (this._state !== this.UNCONNECTED) return;

    this._state = this.CONNECTING;
    var self = this;

    this.info('Initiating handshake with ?', Faye.URI.stringify(this._endpoint));
    this._dispatcher.selectTransport(Faye.MANDATORY_CONNECTION_TYPES);

    this._sendMessage({
      channel:                  Faye.Channel.HANDSHAKE,
      version:                  Faye.BAYEUX_VERSION,
      supportedConnectionTypes: this._dispatcher.getConnectionTypes()

    }, {}, function(response) {

      if (response.successful) {
        this._state = this.CONNECTED;
        this._dispatcher.clientId  = response.clientId;

        this._dispatcher.selectTransport(response.supportedConnectionTypes);

        this.info('Handshake successful: ?', this._dispatcher.clientId);

        this.subscribe(this._channels.getKeys(), true);
        if (callback) Faye.Promise.defer(function() { callback.call(context) });

      } else {
        this.info('Handshake unsuccessful');
        Faye.ENV.setTimeout(function() { self.handshake(callback, context) }, this._dispatcher.retry * 1000);
        this._state = this.UNCONNECTED;
      }
    }, this);
  },

  // Request                              Response
  // MUST include:  * channel             MUST include:  * channel
  //                * clientId                           * successful
  //                * connectionType                     * clientId
  // MAY include:   * ext                 MAY include:   * error
  //                * id                                 * advice
  //                                                     * ext
  //                                                     * id
  //                                                     * timestamp
  connect: function(callback, context) {
    if (this._advice.reconnect === this.NONE) return;
    if (this._state === this.DISCONNECTED) return;

    if (this._state === this.UNCONNECTED)
      return this.handshake(function() { this.connect(callback, context) }, this);

    this.callback(callback, context);
    if (this._state !== this.CONNECTED) return;

    this.info('Calling deferred actions for ?', this._dispatcher.clientId);
    this.setDeferredStatus('succeeded');
    this.setDeferredStatus('unknown');

    if (this._connectRequest) return;
    this._connectRequest = true;

    this.info('Initiating connection for ?', this._dispatcher.clientId);

    this._sendMessage({
      channel:        Faye.Channel.CONNECT,
      clientId:       this._dispatcher.clientId,
      connectionType: this._dispatcher.connectionType

    }, {}, this._cycleConnection, this);
  },

  // Request                              Response
  // MUST include:  * channel             MUST include:  * channel
  //                * clientId                           * successful
  // MAY include:   * ext                                * clientId
  //                * id                  MAY include:   * error
  //                                                     * ext
  //                                                     * id
  disconnect: function() {
    if (this._state !== this.CONNECTED) return;
    this._state = this.DISCONNECTED;

    this.info('Disconnecting ?', this._dispatcher.clientId);
    var promise = new Faye.Publication();

    this._sendMessage({
      channel:  Faye.Channel.DISCONNECT,
      clientId: this._dispatcher.clientId

    }, {}, function(response) {
      if (response.successful) {
        this._dispatcher.close();
        promise.setDeferredStatus('succeeded');
      } else {
        promise.setDeferredStatus('failed', Faye.Error.parse(response.error));
      }
    }, this);

    this.info('Clearing channel listeners for ?', this._dispatcher.clientId);
    this._channels = new Faye.Channel.Set();

    return promise;
  },

  // Request                              Response
  // MUST include:  * channel             MUST include:  * channel
  //                * clientId                           * successful
  //                * subscription                       * clientId
  // MAY include:   * ext                                * subscription
  //                * id                  MAY include:   * error
  //                                                     * advice
  //                                                     * ext
  //                                                     * id
  //                                                     * timestamp
  subscribe: function(channel, callback, context) {
    if (channel instanceof Array)
      return Faye.map(channel, function(c) {
        return this.subscribe(c, callback, context);
      }, this);

    var subscription = new Faye.Subscription(this, channel, callback, context),
        force        = (callback === true),
        hasSubscribe = this._channels.hasSubscription(channel);

    if (hasSubscribe && !force) {
      this._channels.subscribe([channel], callback, context);
      subscription.setDeferredStatus('succeeded');
      return subscription;
    }

    this.connect(function() {
      this.info('Client ? attempting to subscribe to ?', this._dispatcher.clientId, channel);
      if (!force) this._channels.subscribe([channel], callback, context);

      this._sendMessage({
        channel:      Faye.Channel.SUBSCRIBE,
        clientId:     this._dispatcher.clientId,
        subscription: channel

      }, {}, function(response) {
        if (!response.successful) {
          subscription.setDeferredStatus('failed', Faye.Error.parse(response.error));
          return this._channels.unsubscribe(channel, callback, context);
        }

        var channels = [].concat(response.subscription);
        this.info('Subscription acknowledged for ? to ?', this._dispatcher.clientId, channels);
        subscription.setDeferredStatus('succeeded');
      }, this);
    }, this);

    return subscription;
  },

  // Request                              Response
  // MUST include:  * channel             MUST include:  * channel
  //                * clientId                           * successful
  //                * subscription                       * clientId
  // MAY include:   * ext                                * subscription
  //                * id                  MAY include:   * error
  //                                                     * advice
  //                                                     * ext
  //                                                     * id
  //                                                     * timestamp
  unsubscribe: function(channel, callback, context) {
    if (channel instanceof Array)
      return Faye.map(channel, function(c) {
        return this.unsubscribe(c, callback, context);
      }, this);

    var dead = this._channels.unsubscribe(channel, callback, context);
    if (!dead) return;

    this.connect(function() {
      this.info('Client ? attempting to unsubscribe from ?', this._dispatcher.clientId, channel);

      this._sendMessage({
        channel:      Faye.Channel.UNSUBSCRIBE,
        clientId:     this._dispatcher.clientId,
        subscription: channel

      }, {}, function(response) {
        if (!response.successful) return;

        var channels = [].concat(response.subscription);
        this.info('Unsubscription acknowledged for ? from ?', this._dispatcher.clientId, channels);
      }, this);
    }, this);
  },

  // Request                              Response
  // MUST include:  * channel             MUST include:  * channel
  //                * data                               * successful
  // MAY include:   * clientId            MAY include:   * id
  //                * id                                 * error
  //                * ext                                * ext
  publish: function(channel, data, options) {
    Faye.validateOptions(options || {}, ['attempts', 'deadline']);
    var publication = new Faye.Publication();

    this.connect(function() {
      this.info('Client ? queueing published message to ?: ?', this._dispatcher.clientId, channel, data);

      this._sendMessage({
        channel:  channel,
        data:     data,
        clientId: this._dispatcher.clientId

      }, options, function(response) {
        if (response.successful)
          publication.setDeferredStatus('succeeded');
        else
          publication.setDeferredStatus('failed', Faye.Error.parse(response.error));
      }, this);
    }, this);

    return publication;
  },

  _sendMessage: function(message, options, callback, context) {
    message.id = this._generateMessageId();

    var timeout = this._advice.timeout
                ? 1.2 * this._advice.timeout / 1000
                : 1.2 * this._dispatcher.retry;

    this.pipeThroughExtensions('outgoing', message, null, function(message) {
      if (!message) return;
      if (callback) this._responseCallbacks[message.id] = [callback, context];
      this._dispatcher.sendMessage(message, timeout, options || {});
    }, this);
  },

  _generateMessageId: function() {
    this._messageId += 1;
    if (this._messageId >= Math.pow(2,32)) this._messageId = 0;
    return this._messageId.toString(36);
  },

  _receiveMessage: function(message) {
    var id = message.id, callback;

    if (message.successful !== undefined) {
      callback = this._responseCallbacks[id];
      delete this._responseCallbacks[id];
    }

    this.pipeThroughExtensions('incoming', message, null, function(message) {
      if (!message) return;
      if (message.advice) this._handleAdvice(message.advice);
      this._deliverMessage(message);
      if (callback) callback[0].call(callback[1], message);
    }, this);
  },

  _handleAdvice: function(advice) {
    Faye.extend(this._advice, advice);
    this._dispatcher.timeout = this._advice.timeout / 1000;

    if (this._advice.reconnect === this.HANDSHAKE && this._state !== this.DISCONNECTED) {
      this._state = this.UNCONNECTED;
      this._dispatcher.clientId = null;
      this._cycleConnection();
    }
  },

  _deliverMessage: function(message) {
    if (!message.channel || message.data === undefined) return;
    this.info('Client ? calling listeners for ? with ?', this._dispatcher.clientId, message.channel, message.data);
    this._channels.distributeMessage(message);
  },

  _cycleConnection: function() {
    if (this._connectRequest) {
      this._connectRequest = null;
      this.info('Closed connection for ?', this._dispatcher.clientId);
    }
    var self = this;
    Faye.ENV.setTimeout(function() { self.connect() }, this._advice.interval);
  }
});

Faye.extend(Faye.Client.prototype, Faye.Deferrable);
Faye.extend(Faye.Client.prototype, Faye.Publisher);
Faye.extend(Faye.Client.prototype, Faye.Logging);
Faye.extend(Faye.Client.prototype, Faye.Extensible);

Faye.Dispatcher = Faye.Class({
  MAX_REQUEST_SIZE: 2048,
  DEFAULT_RETRY:    5,

  UP:   1,
  DOWN: 2,

  initialize: function(client, endpoint, options) {
    this._client     = client;
    this.endpoint    = Faye.URI.parse(endpoint);
    this._alternates = options.endpoints || {};

    this.cookies      = Faye.Cookies && new Faye.Cookies.CookieJar();
    this._disabled    = [];
    this._envelopes   = {};
    this.headers      = {};
    this.retry        = options.retry || this.DEFAULT_RETRY;
    this._scheduler   = options.scheduler || Faye.Scheduler;
    this._state       = 0;
    this.transports   = {};
    this.wsExtensions = [];

    this.proxy = options.proxy || {};
    if (typeof this._proxy === 'string') this._proxy = {origin: this._proxy};

    var exts = options.websocketExtensions;
    if (exts) {
      exts = [].concat(exts);
      for (var i = 0, n = exts.length; i < n; i++)
        this.addWebsocketExtension(exts[i]);
    }

    this.tls = options.tls || {};
    this.tls.ca = this.tls.ca || options.ca;

    for (var type in this._alternates)
      this._alternates[type] = Faye.URI.parse(this._alternates[type]);

    this.maxRequestSize = this.MAX_REQUEST_SIZE;
  },

  endpointFor: function(connectionType) {
    return this._alternates[connectionType] || this.endpoint;
  },

  addWebsocketExtension: function(extension) {
    this.wsExtensions.push(extension);
  },

  disable: function(feature) {
    this._disabled.push(feature);
  },

  setHeader: function(name, value) {
    this.headers[name] = value;
  },

  close: function() {
    var transport = this._transport;
    delete this._transport;
    if (transport) transport.close();
  },

  getConnectionTypes: function() {
    return Faye.Transport.getConnectionTypes();
  },

  selectTransport: function(transportTypes) {
    Faye.Transport.get(this, transportTypes, this._disabled, function(transport) {
      this.debug('Selected ? transport for ?', transport.connectionType, Faye.URI.stringify(transport.endpoint));

      if (transport === this._transport) return;
      if (this._transport) this._transport.close();

      this._transport = transport;
      this.connectionType = transport.connectionType;
    }, this);
  },

  sendMessage: function(message, timeout, options) {
    options = options || {};

    var id       = message.id,
        attempts = options.attempts,
        deadline = options.deadline && new Date().getTime() + (options.deadline * 1000),
        envelope = this._envelopes[id],
        scheduler;

    if (!envelope) {
      scheduler = new this._scheduler(message, {timeout: timeout, interval: this.retry, attempts: attempts, deadline: deadline});
      envelope  = this._envelopes[id] = {message: message, scheduler: scheduler};
    }

    this._sendEnvelope(envelope);
  },

  _sendEnvelope: function(envelope) {
    if (!this._transport) return;
    if (envelope.request || envelope.timer) return;

    var message   = envelope.message,
        scheduler = envelope.scheduler,
        self      = this;

    if (!scheduler.isDeliverable()) {
      scheduler.abort();
      delete this._envelopes[message.id];
      return;
    }

    envelope.timer = Faye.ENV.setTimeout(function() {
      self.handleError(message);
    }, scheduler.getTimeout() * 1000);

    scheduler.send();
    envelope.request = this._transport.sendMessage(message);
  },

  handleResponse: function(reply) {
    var envelope = this._envelopes[reply.id];

    if (reply.successful !== undefined && envelope) {
      envelope.scheduler.succeed();
      delete this._envelopes[reply.id];
      Faye.ENV.clearTimeout(envelope.timer);
    }

    this.trigger('message', reply);

    if (this._state === this.UP) return;
    this._state = this.UP;
    this._client.trigger('transport:up');
  },

  handleError: function(message, immediate) {
    var envelope = this._envelopes[message.id],
        request  = envelope && envelope.request,
        self     = this;

    if (!request) return;

    request.then(function(req) {
      if (req && req.abort) req.abort();
    });

    var scheduler = envelope.scheduler;
    scheduler.fail();

    Faye.ENV.clearTimeout(envelope.timer);
    envelope.request = envelope.timer = null;

    if (immediate) {
      this._sendEnvelope(envelope);
    } else {
      envelope.timer = Faye.ENV.setTimeout(function() {
        envelope.timer = null;
        self._sendEnvelope(envelope);
      }, scheduler.getInterval() * 1000);
    }

    if (this._state === this.DOWN) return;
    this._state = this.DOWN;
    this._client.trigger('transport:down');
  }
});

Faye.extend(Faye.Dispatcher.prototype, Faye.Publisher);
Faye.extend(Faye.Dispatcher.prototype, Faye.Logging);

Faye.Scheduler = function(message, options) {
  this.message  = message;
  this.options  = options;
  this.attempts = 0;
};

Faye.extend(Faye.Scheduler.prototype, {
  getTimeout: function() {
    return this.options.timeout;
  },

  getInterval: function() {
    return this.options.interval;
  },

  isDeliverable: function() {
    var attempts = this.options.attempts,
        made     = this.attempts,
        deadline = this.options.deadline,
        now      = new Date().getTime();

    if (attempts !== undefined && made >= attempts)
      return false;

    if (deadline !== undefined && now > deadline)
      return false;

    return true;
  },

  send: function() {
    this.attempts += 1;
  },

  succeed: function() {},

  fail: function() {},

  abort: function() {}
});

Faye.Transport = Faye.extend(Faye.Class({
  DEFAULT_PORTS:    {'http:': 80, 'https:': 443, 'ws:': 80, 'wss:': 443},
  SECURE_PROTOCOLS: ['https:', 'wss:'],
  MAX_DELAY:        0,

  batching:  true,

  initialize: function(dispatcher, endpoint) {
    this._dispatcher = dispatcher;
    this.endpoint    = endpoint;
    this._outbox     = [];
    this._proxy      = Faye.extend({}, this._dispatcher.proxy);

    if (!this._proxy.origin && Faye.NodeAdapter) {
      this._proxy.origin = Faye.indexOf(this.SECURE_PROTOCOLS, this.endpoint.protocol) >= 0
                         ? (process.env.HTTPS_PROXY || process.env.https_proxy)
                         : (process.env.HTTP_PROXY  || process.env.http_proxy);
    }
  },

  close: function() {},

  encode: function(messages) {
    return '';
  },

  sendMessage: function(message) {
    this.debug('Client ? sending message to ?: ?',
               this._dispatcher.clientId, Faye.URI.stringify(this.endpoint), message);

    if (!this.batching) return Faye.Promise.fulfilled(this.request([message]));

    this._outbox.push(message);
    this._flushLargeBatch();
    this._promise = this._promise || new Faye.Promise();

    if (message.channel === Faye.Channel.HANDSHAKE) {
      this.addTimeout('publish', 0.01, this._flush, this);
      return this._promise;
    }

    if (message.channel === Faye.Channel.CONNECT)
      this._connectMessage = message;

    this.addTimeout('publish', this.MAX_DELAY, this._flush, this);
    return this._promise;
  },

  _flush: function() {
    this.removeTimeout('publish');

    if (this._outbox.length > 1 && this._connectMessage)
      this._connectMessage.advice = {timeout: 0};

    Faye.Promise.fulfill(this._promise, this.request(this._outbox));
    delete this._promise;

    this._connectMessage = null;
    this._outbox = [];
  },

  _flushLargeBatch: function() {
    var string = this.encode(this._outbox);
    if (string.length < this._dispatcher.maxRequestSize) return;
    var last = this._outbox.pop();
    this._flush();
    if (last) this._outbox.push(last);
  },

  _receive: function(replies) {
    if (!replies) return;
    replies = [].concat(replies);

    this.debug('Client ? received from ? via ?: ?',
               this._dispatcher.clientId, Faye.URI.stringify(this.endpoint), this.connectionType, replies);

    for (var i = 0, n = replies.length; i < n; i++)
      this._dispatcher.handleResponse(replies[i]);
  },

  _handleError: function(messages, immediate) {
    messages = [].concat(messages);

    this.debug('Client ? failed to send to ? via ?: ?',
               this._dispatcher.clientId, Faye.URI.stringify(this.endpoint), this.connectionType, messages);

    for (var i = 0, n = messages.length; i < n; i++)
      this._dispatcher.handleError(messages[i]);
  },

  _getCookies: function() {
    var cookies = this._dispatcher.cookies,
        url     = Faye.URI.stringify(this.endpoint);

    if (!cookies) return '';

    return Faye.map(cookies.getCookiesSync(url), function(cookie) {
      return cookie.cookieString();
    }).join('; ');
  },

  _storeCookies: function(setCookie) {
    var cookies = this._dispatcher.cookies,
        url     = Faye.URI.stringify(this.endpoint),
        cookie;

    if (!setCookie || !cookies) return;
    setCookie = [].concat(setCookie);

    for (var i = 0, n = setCookie.length; i < n; i++) {
      cookie = Faye.Cookies.Cookie.parse(setCookie[i]);
      cookies.setCookieSync(cookie, url);
    }
  }

}), {
  get: function(dispatcher, allowed, disabled, callback, context) {
    var endpoint = dispatcher.endpoint;

    Faye.asyncEach(this._transports, function(pair, resume) {
      var connType     = pair[0], klass = pair[1],
          connEndpoint = dispatcher.endpointFor(connType);

      if (Faye.indexOf(disabled, connType) >= 0)
        return resume();

      if (Faye.indexOf(allowed, connType) < 0) {
        klass.isUsable(dispatcher, connEndpoint, function() {});
        return resume();
      }

      klass.isUsable(dispatcher, connEndpoint, function(isUsable) {
        if (!isUsable) return resume();
        var transport = klass.hasOwnProperty('create') ? klass.create(dispatcher, connEndpoint) : new klass(dispatcher, connEndpoint);
        callback.call(context, transport);
      });
    }, function() {
      throw new Error('Could not find a usable connection type for ' + Faye.URI.stringify(endpoint));
    });
  },

  register: function(type, klass) {
    this._transports.push([type, klass]);
    klass.prototype.connectionType = type;
  },

  getConnectionTypes: function() {
    return Faye.map(this._transports, function(t) { return t[0] });
  },

  _transports: []
});

Faye.extend(Faye.Transport.prototype, Faye.Logging);
Faye.extend(Faye.Transport.prototype, Faye.Timeouts);

Faye.Engine = {
  get: function(options) {
    return new Faye.Engine.Proxy(options);
  },

  METHODS: ['createClient', 'clientExists', 'destroyClient', 'ping', 'subscribe', 'unsubscribe']
};

Faye.Engine.Proxy = Faye.Class({
  MAX_DELAY:  0,
  INTERVAL:   0,
  TIMEOUT:    60,

  className: 'Engine',

  initialize: function(options) {
    this._options     = options || {};
    this._connections = {};
    this.interval     = this._options.interval || this.INTERVAL;
    this.timeout      = this._options.timeout  || this.TIMEOUT;

    var engineClass = this._options.type || Faye.Engine.Memory;
    this._engine    = engineClass.create(this, this._options);

    this.bind('close', function(clientId) {
      var self = this;
      Faye.Promise.defer(function() { self.flushConnection(clientId) });
    }, this);

    this.debug('Created new engine: ?', this._options);
  },

  connect: function(clientId, options, callback, context) {
    this.debug('Accepting connection from ?', clientId);
    this._engine.ping(clientId);
    var conn = this.connection(clientId, true);
    conn.connect(options, callback, context);
    this._engine.emptyQueue(clientId);
  },

  hasConnection: function(clientId) {
    return this._connections.hasOwnProperty(clientId);
  },

  connection: function(clientId, create) {
    var conn = this._connections[clientId];
    if (conn || !create) return conn;
    this._connections[clientId] = new Faye.Engine.Connection(this, clientId);
    this.trigger('connection:open', clientId);
    return this._connections[clientId];
  },

  closeConnection: function(clientId) {
    this.debug('Closing connection for ?', clientId);
    var conn = this._connections[clientId];
    if (!conn) return;
    if (conn.socket) conn.socket.close();
    this.trigger('connection:close', clientId);
    delete this._connections[clientId];
  },

  openSocket: function(clientId, socket) {
    var conn = this.connection(clientId, true);
    conn.socket = socket;
  },

  deliver: function(clientId, messages) {
    if (!messages || messages.length === 0) return false;

    var conn = this.connection(clientId, false);
    if (!conn) return false;

    for (var i = 0, n = messages.length; i < n; i++) {
      conn.deliver(messages[i]);
    }
    return true;
  },

  generateId: function() {
    return Faye.random();
  },

  flushConnection: function(clientId, close) {
    if (!clientId) return;
    this.debug('Flushing connection for ?', clientId);
    var conn = this.connection(clientId, false);
    if (!conn) return;
    if (close === false) conn.socket = null;
    conn.flush();
    this.closeConnection(clientId);
  },

  close: function() {
    for (var clientId in this._connections) this.flushConnection(clientId);
    this._engine.disconnect();
  },

  disconnect: function() {
    if (this._engine.disconnect) return this._engine.disconnect();
  },

  publish: function(message) {
    var channels = Faye.Channel.expand(message.channel);
    return this._engine.publish(message, channels);
  }
});

Faye.Engine.METHODS.forEach(function(method) {
  Faye.Engine.Proxy.prototype[method] = function() {
    return this._engine[method].apply(this._engine, arguments);
  };
});

Faye.extend(Faye.Engine.Proxy.prototype, Faye.Publisher);
Faye.extend(Faye.Engine.Proxy.prototype, Faye.Logging);

Faye.Engine.Connection = Faye.Class({
  initialize: function(engine, id, options) {
    this._engine  = engine;
    this._id      = id;
    this._options = options;
    this._inbox   = [];
  },

  deliver: function(message) {
    delete message.clientId;
    if (this.socket) return this.socket.send(message);
    this._inbox.push(message);
    this._beginDeliveryTimeout();
  },

  connect: function(options, callback, context) {
    options = options || {};
    var timeout = (options.timeout !== undefined) ? options.timeout / 1000 : this._engine.timeout;

    this.setDeferredStatus('unknown');
    this.callback(callback, context);

    this._beginDeliveryTimeout();
    this._beginConnectionTimeout(timeout);
  },

  flush: function() {
    this.removeTimeout('connection');
    this.removeTimeout('delivery');

    this.setDeferredStatus('succeeded', this._inbox);
    this._inbox = [];

    if (!this.socket) this._engine.closeConnection(this._id);
  },

  _beginDeliveryTimeout: function() {
    if (this._inbox.length === 0) return;
    this.addTimeout('delivery', this._engine.MAX_DELAY, this.flush, this);
  },

  _beginConnectionTimeout: function(timeout) {
    this.addTimeout('connection', timeout, this.flush, this);
  }
});

Faye.extend(Faye.Engine.Connection.prototype, Faye.Deferrable);
Faye.extend(Faye.Engine.Connection.prototype, Faye.Timeouts);

Faye.Engine.Memory = function(server, options) {
  this._server    = server;
  this._options   = options || {};
  this.reset();
};

Faye.Engine.Memory.create = function(server, options) {
  return new this(server, options);
};

Faye.Engine.Memory.prototype = {
  disconnect: function() {
    this.reset();
    this.removeAllTimeouts();
  },

  reset: function() {
    this._namespace = new Faye.Namespace();
    this._clients   = {};
    this._channels  = {};
    this._messages  = {};
  },

  createClient: function(callback, context) {
    var clientId = this._namespace.generate();
    this._server.debug('Created new client ?', clientId);
    this.ping(clientId);
    this._server.trigger('handshake', clientId);
    callback.call(context, clientId);
  },

  destroyClient: function(clientId, callback, context) {
    if (!this._namespace.exists(clientId)) return;
    var clients = this._clients;

    if (clients[clientId])
      clients[clientId].forEach(function(channel) { this.unsubscribe(clientId, channel) }, this);

    this.removeTimeout(clientId);
    this._namespace.release(clientId);
    delete this._messages[clientId];
    this._server.debug('Destroyed client ?', clientId);
    this._server.trigger('disconnect', clientId);
    this._server.trigger('close', clientId);
    if (callback) callback.call(context);
  },

  clientExists: function(clientId, callback, context) {
    callback.call(context, this._namespace.exists(clientId));
  },

  ping: function(clientId) {
    var timeout = this._server.timeout;
    if (typeof timeout !== 'number') return;

    this._server.debug('Ping ?, ?', clientId, timeout);
    this.removeTimeout(clientId);
    this.addTimeout(clientId, 2 * timeout, function() {
      this.destroyClient(clientId);
    }, this);
  },

  subscribe: function(clientId, channel, callback, context) {
    var clients = this._clients, channels = this._channels;

    clients[clientId] = clients[clientId] || new Faye.Set();
    var trigger = clients[clientId].add(channel);

    channels[channel] = channels[channel] || new Faye.Set();
    channels[channel].add(clientId);

    this._server.debug('Subscribed client ? to channel ?', clientId, channel);
    if (trigger) this._server.trigger('subscribe', clientId, channel);
    if (callback) callback.call(context, true);
  },

  unsubscribe: function(clientId, channel, callback, context) {
    var clients  = this._clients,
        channels = this._channels,
        trigger  = false;

    if (clients[clientId]) {
      trigger = clients[clientId].remove(channel);
      if (clients[clientId].isEmpty()) delete clients[clientId];
    }

    if (channels[channel]) {
      channels[channel].remove(clientId);
      if (channels[channel].isEmpty()) delete channels[channel];
    }

    this._server.debug('Unsubscribed client ? from channel ?', clientId, channel);
    if (trigger) this._server.trigger('unsubscribe', clientId, channel);
    if (callback) callback.call(context, true);
  },

  publish: function(message, channels) {
    this._server.debug('Publishing message ?', message);

    var messages = this._messages,
        clients  = new Faye.Set(),
        subs;

    for (var i = 0, n = channels.length; i < n; i++) {
      subs = this._channels[channels[i]];
      if (!subs) continue;
      subs.forEach(clients.add, clients);
    }

    clients.forEach(function(clientId) {
      this._server.debug('Queueing for client ?: ?', clientId, message);
      messages[clientId] = messages[clientId] || [];
      messages[clientId].push(Faye.copyObject(message));
      this.emptyQueue(clientId);
    }, this);

    this._server.trigger('publish', message.clientId, message.channel, message.data);
  },

  emptyQueue: function(clientId) {
    if (!this._server.hasConnection(clientId)) return;
    this._server.deliver(clientId, this._messages[clientId]);
    delete this._messages[clientId];
  }
};
Faye.extend(Faye.Engine.Memory.prototype, Faye.Timeouts);

Faye.Server = Faye.Class({
  META_METHODS: ['handshake', 'connect', 'disconnect', 'subscribe', 'unsubscribe'],

  initialize: function(options) {
    this._options  = options || {};
    var engineOpts = this._options.engine || {};
    engineOpts.timeout = this._options.timeout;
    this._engine   = Faye.Engine.get(engineOpts);

    this.info('Created new server: ?', this._options);
  },

  close: function() {
    return this._engine.close();
  },

  openSocket: function(clientId, socket, request) {
    if (!clientId || !socket) return;
    this._engine.openSocket(clientId, new Faye.Server.Socket(this, socket, request));
  },

  closeSocket: function(clientId, close) {
    this._engine.flushConnection(clientId, close);
  },

  process: function(messages, request, callback, context) {
    var local = (request === null);

    messages = [].concat(messages);
    this.info('Processing messages: ? (local: ?)', messages, local);

    if (messages.length === 0) return callback.call(context, []);
    var processed = 0, responses = [], self = this;

    var gatherReplies = function(replies) {
      responses = responses.concat(replies);
      processed += 1;
      if (processed < messages.length) return;

      var n = responses.length;
      while (n--) {
        if (!responses[n]) responses.splice(n,1);
      }
      self.info('Returning replies: ?', responses);
      callback.call(context, responses);
    };

    var handleReply = function(replies) {
      var extended = 0, expected = replies.length;
      if (expected === 0) gatherReplies(replies);

      for (var i = 0, n = replies.length; i < n; i++) {
        this.debug('Processing reply: ?', replies[i]);
        (function(index) {
          self.pipeThroughExtensions('outgoing', replies[index], request, function(message) {
            replies[index] = message;
            extended += 1;
            if (extended === expected) gatherReplies(replies);
          });
        })(i);
      }
    };

    for (var i = 0, n = messages.length; i < n; i++) {
      this.pipeThroughExtensions('incoming', messages[i], request, function(pipedMessage) {
        this._handle(pipedMessage, local, handleReply, this);
      }, this);
    }
  },

  _makeResponse: function(message) {
    var response = {};

    if (message.id)       response.id       = message.id;
    if (message.clientId) response.clientId = message.clientId;
    if (message.channel)  response.channel  = message.channel;
    if (message.error)    response.error    = message.error;

    response.successful = !response.error;
    return response;
  },

  _handle: function(message, local, callback, context) {
    if (!message) return callback.call(context, []);
    this.info('Handling message: ? (local: ?)', message, local);

    var channelName = message.channel,
        error       = message.error,
        response;

    if (Faye.Channel.isMeta(channelName))
      return this._handleMeta(message, local, callback, context);

    if (!Faye.Grammar.CHANNEL_NAME.test(channelName))
      error = Faye.Error.channelInvalid(channelName);

    if (!error) this._engine.publish(message);

    response = this._makeResponse(message);
    if (error) response.error = error;
    response.successful = !response.error;
    callback.call(context, [response]);
  },

  _handleMeta: function(message, local, callback, context) {
    var method = Faye.Channel.parse(message.channel)[1],
        response;

    if (Faye.indexOf(this.META_METHODS, method) < 0) {
      response = this._makeResponse(message);
      response.error = Faye.Error.channelForbidden(message.channel);
      response.successful = false;
      return callback.call(context, [response]);
    }

    this[method](message, local, function(responses) {
      responses = [].concat(responses);
      for (var i = 0, n = responses.length; i < n; i++) this._advize(responses[i], message.connectionType);
      callback.call(context, responses);
    }, this);
  },

  _advize: function(response, connectionType) {
    if (Faye.indexOf([Faye.Channel.HANDSHAKE, Faye.Channel.CONNECT], response.channel) < 0)
      return;

    var interval, timeout;
    if (connectionType === 'eventsource') {
      interval = Math.floor(this._engine.timeout * 1000);
      timeout  = 0;
    } else {
      interval = Math.floor(this._engine.interval * 1000);
      timeout  = Math.floor(this._engine.timeout * 1000);
    }

    response.advice = response.advice || {};
    if (response.error) {
      Faye.extend(response.advice, {reconnect:  'handshake'}, false);
    } else {
      Faye.extend(response.advice, {
        reconnect:  'retry',
        interval:   interval,
        timeout:    timeout
      }, false);
    }
  },

  // MUST contain  * version
  //               * supportedConnectionTypes
  // MAY contain   * minimumVersion
  //               * ext
  //               * id
  handshake: function(message, local, callback, context) {
    var response = this._makeResponse(message);
    response.version = Faye.BAYEUX_VERSION;

    if (!message.version)
      response.error = Faye.Error.parameterMissing('version');

    var clientConns = message.supportedConnectionTypes,
        commonConns;

    response.supportedConnectionTypes = Faye.CONNECTION_TYPES;

    if (clientConns) {
      commonConns = Faye.filter(clientConns, function(conn) {
        return Faye.indexOf(Faye.CONNECTION_TYPES, conn) >= 0;
      });
      if (commonConns.length === 0)
        response.error = Faye.Error.conntypeMismatch(clientConns);
    } else {
      response.error = Faye.Error.parameterMissing('supportedConnectionTypes');
    }

    response.successful = !response.error;
    if (!response.successful) return callback.call(context, response);

    this._engine.createClient(function(clientId) {
      response.clientId = clientId;
      callback.call(context, response);
    }, this);
  },

  // MUST contain  * clientId
  //               * connectionType
  // MAY contain   * ext
  //               * id
  connect: function(message, local, callback, context) {
    var response       = this._makeResponse(message),
        clientId       = message.clientId,
        connectionType = message.connectionType;

    this._engine.clientExists(clientId, function(exists) {
      if (!exists)         response.error = Faye.Error.clientUnknown(clientId);
      if (!clientId)       response.error = Faye.Error.parameterMissing('clientId');

      if (Faye.indexOf(Faye.CONNECTION_TYPES, connectionType) < 0)
        response.error = Faye.Error.conntypeMismatch(connectionType);

      if (!connectionType) response.error = Faye.Error.parameterMissing('connectionType');

      response.successful = !response.error;

      if (!response.successful) {
        delete response.clientId;
        return callback.call(context, response);
      }

      if (message.connectionType === 'eventsource') {
        message.advice = message.advice || {};
        message.advice.timeout = 0;
      }
      this._engine.connect(response.clientId, message.advice, function(events) {
        callback.call(context, [response].concat(events));
      });
    }, this);
  },

  // MUST contain  * clientId
  // MAY contain   * ext
  //               * id
  disconnect: function(message, local, callback, context) {
    var response = this._makeResponse(message),
        clientId = message.clientId;

    this._engine.clientExists(clientId, function(exists) {
      if (!exists)   response.error = Faye.Error.clientUnknown(clientId);
      if (!clientId) response.error = Faye.Error.parameterMissing('clientId');

      response.successful = !response.error;
      if (!response.successful) delete response.clientId;

      if (response.successful) this._engine.destroyClient(clientId);
      callback.call(context, response);
    }, this);
  },

  // MUST contain  * clientId
  //               * subscription
  // MAY contain   * ext
  //               * id
  subscribe: function(message, local, callback, context) {
    var response     = this._makeResponse(message),
        clientId     = message.clientId,
        subscription = message.subscription,
        channel;

    subscription = subscription ? [].concat(subscription) : [];

    this._engine.clientExists(clientId, function(exists) {
      if (!exists)               response.error = Faye.Error.clientUnknown(clientId);
      if (!clientId)             response.error = Faye.Error.parameterMissing('clientId');
      if (!message.subscription) response.error = Faye.Error.parameterMissing('subscription');

      response.subscription = message.subscription || [];

      for (var i = 0, n = subscription.length; i < n; i++) {
        channel = subscription[i];

        if (response.error) break;
        if (!local && !Faye.Channel.isSubscribable(channel)) response.error = Faye.Error.channelForbidden(channel);
        if (!Faye.Channel.isValid(channel))                  response.error = Faye.Error.channelInvalid(channel);

        if (response.error) break;
        this._engine.subscribe(clientId, channel);
      }

      response.successful = !response.error;
      callback.call(context, response);
    }, this);
  },

  // MUST contain  * clientId
  //               * subscription
  // MAY contain   * ext
  //               * id
  unsubscribe: function(message, local, callback, context) {
    var response     = this._makeResponse(message),
        clientId     = message.clientId,
        subscription = message.subscription,
        channel;

    subscription = subscription ? [].concat(subscription) : [];

    this._engine.clientExists(clientId, function(exists) {
      if (!exists)               response.error = Faye.Error.clientUnknown(clientId);
      if (!clientId)             response.error = Faye.Error.parameterMissing('clientId');
      if (!message.subscription) response.error = Faye.Error.parameterMissing('subscription');

      response.subscription = message.subscription || [];

      for (var i = 0, n = subscription.length; i < n; i++) {
        channel = subscription[i];

        if (response.error) break;
        if (!local && !Faye.Channel.isSubscribable(channel)) response.error = Faye.Error.channelForbidden(channel);
        if (!Faye.Channel.isValid(channel))                  response.error = Faye.Error.channelInvalid(channel);

        if (response.error) break;
        this._engine.unsubscribe(clientId, channel);
      }

      response.successful = !response.error;
      callback.call(context, response);
    }, this);
  }
});

Faye.extend(Faye.Server.prototype, Faye.Logging);
Faye.extend(Faye.Server.prototype, Faye.Extensible);

Faye.Server.Socket = Faye.Class({
  initialize: function(server, socket, request) {
    this._server  = server;
    this._socket  = socket;
    this._request = request;
  },

  send: function(message) {
    this._server.pipeThroughExtensions('outgoing', message, this._request, function(pipedMessage) {
      if (this._socket)
        this._socket.send(Faye.toJSON([pipedMessage]));
    }, this);
  },

  close: function() {
    if (this._socket) this._socket.close();
    delete this._socket;
  }
});

Faye.Transport.NodeLocal = Faye.extend(Faye.Class(Faye.Transport, {
  batching: false,

  request: function(messages) {
    messages = Faye.copyObject(messages);
    this.endpoint.process(messages, null, function(replies) {
      this._receive(Faye.copyObject(replies));
    }, this);
  }
}), {
  isUsable: function(client, endpoint, callback, context) {
    callback.call(context, endpoint instanceof Faye.Server);
  }
});

Faye.Transport.register('in-process', Faye.Transport.NodeLocal);

Faye.Transport.WebSocket = Faye.extend(Faye.Class(Faye.Transport, {
  UNCONNECTED:  1,
  CONNECTING:   2,
  CONNECTED:    3,

  batching:     false,

  isUsable: function(callback, context) {
    this.callback(function() { callback.call(context, true) });
    this.errback(function() { callback.call(context, false) });
    this.connect();
  },

  request: function(messages) {
    this._pending = this._pending || new Faye.Set();
    for (var i = 0, n = messages.length; i < n; i++) this._pending.add(messages[i]);

    var promise = new Faye.Promise();

    this.callback(function(socket) {
      if (!socket) return;
      socket.send(Faye.toJSON(messages));
      Faye.Promise.fulfill(promise, socket);
    }, this);

    this.connect();

    return {
      abort: function() { promise.then(function(ws) { ws.close() }) }
    };
  },

  connect: function() {
    if (Faye.Transport.WebSocket._unloaded) return;

    this._state = this._state || this.UNCONNECTED;
    if (this._state !== this.UNCONNECTED) return;
    this._state = this.CONNECTING;

    var socket = this._createSocket();
    if (!socket) return this.setDeferredStatus('failed');

    var self = this;

    socket.onopen = function() {
      if (socket.headers) self._storeCookies(socket.headers['set-cookie']);
      self._socket = socket;
      self._state = self.CONNECTED;
      self._everConnected = true;
      self._ping();
      self.setDeferredStatus('succeeded', socket);
    };

    var closed = false;
    socket.onclose = socket.onerror = function() {
      if (closed) return;
      closed = true;

      var wasConnected = (self._state === self.CONNECTED);
      socket.onopen = socket.onclose = socket.onerror = socket.onmessage = null;

      delete self._socket;
      self._state = self.UNCONNECTED;
      self.removeTimeout('ping');
      self.setDeferredStatus('unknown');

      var pending = self._pending ? self._pending.toArray() : [];
      delete self._pending;

      if (wasConnected) {
        self._handleError(pending, true);
      } else if (self._everConnected) {
        self._handleError(pending);
      } else {
        self.setDeferredStatus('failed');
      }
    };

    socket.onmessage = function(event) {
      var replies = JSON.parse(event.data);
      if (!replies) return;

      replies = [].concat(replies);

      for (var i = 0, n = replies.length; i < n; i++) {
        if (replies[i].successful === undefined) continue;
        self._pending.remove(replies[i]);
      }
      self._receive(replies);
    };
  },

  close: function() {
    if (!this._socket) return;
    this._socket.close();
  },

  _createSocket: function() {
    var url        = Faye.Transport.WebSocket.getSocketUrl(this.endpoint),
        headers    = this._dispatcher.headers,
        extensions = this._dispatcher.wsExtensions,
        cookie     = this._getCookies(),
        tls        = this._dispatcher.tls,
        options    = {extensions: extensions, headers: headers, proxy: this._proxy, tls: tls};

    if (cookie !== '') options.headers['Cookie'] = cookie;

    if (Faye.WebSocket)        return new Faye.WebSocket.Client(url, [], options);
    if (Faye.ENV.MozWebSocket) return new MozWebSocket(url);
    if (Faye.ENV.WebSocket)    return new WebSocket(url);
  },

  _ping: function() {
    if (!this._socket) return;
    this._socket.send('[]');
    this.addTimeout('ping', this._dispatcher.timeout / 2, this._ping, this);
  }

}), {
  PROTOCOLS: {
    'http:':  'ws:',
    'https:': 'wss:'
  },

  create: function(dispatcher, endpoint) {
    var sockets = dispatcher.transports.websocket = dispatcher.transports.websocket || {};
    sockets[endpoint.href] = sockets[endpoint.href] || new this(dispatcher, endpoint);
    return sockets[endpoint.href];
  },

  getSocketUrl: function(endpoint) {
    endpoint = Faye.copyObject(endpoint);
    endpoint.protocol = this.PROTOCOLS[endpoint.protocol];
    return Faye.URI.stringify(endpoint);
  },

  isUsable: function(dispatcher, endpoint, callback, context) {
    this.create(dispatcher, endpoint).isUsable(callback, context);
  }
});

Faye.extend(Faye.Transport.WebSocket.prototype, Faye.Deferrable);
Faye.Transport.register('websocket', Faye.Transport.WebSocket);

if (Faye.Event && Faye.ENV.onbeforeunload !== undefined)
  Faye.Event.on(Faye.ENV, 'beforeunload', function() {
    Faye.Transport.WebSocket._unloaded = true;
  });

Faye.Transport.NodeHttp = Faye.extend(Faye.Class(Faye.Transport, {
  initialize: function() {
    Faye.Transport.prototype.initialize.apply(this, arguments);

    this._endpointSecure = (this.SECURE_PROTOCOLS.indexOf(this.endpoint.protocol) >= 0);
    this._httpClient     = this._endpointSecure ? https : http;

    var proxy = this._proxy;
    if (!proxy.origin) return;

    this._proxyUri    = url.parse(proxy.origin);
    this._proxySecure = (this.SECURE_PROTOCOLS.indexOf(this._proxyUri.protocol) >= 0);

    if (!this._endpointSecure) {
      this._httpClient = this._proxySecure ? https : http;
      return;
    }

    var options = Faye.extend({
      proxy: {
        host:       this._proxyUri.hostname,
        port:       this._proxyUri.port || this.DEFAULT_PORTS[this._proxyUri.protocol],
        proxyAuth:  this._proxyUri.auth,
        headers:    Faye.extend({host: this.endpoint.host}, proxy.headers)
      }
    }, this._dispatcher.tls);

    if (this._proxySecure) {
      Faye.extend(options.proxy, proxy.tls);
      this._tunnel = tunnel.httpsOverHttps(options);
    } else {
      this._tunnel = tunnel.httpsOverHttp(options);
    }
  },

  encode: function(messages) {
    return Faye.toJSON(messages);
  },

  request: function(messages) {
    var content = new Buffer(this.encode(messages), 'utf8'),
        params  = this._buildParams(content),
        request = this._httpClient.request(params),
        self    = this;

    request.on('response', function(response) {
      self._handleResponse(messages, response);
      self._storeCookies(response.headers['set-cookie']);
    });

    request.on('error', function(error) {
      self._handleError(messages);
    });

    request.end(content);
    return request;
  },

  _buildParams: function(content) {
    var uri    = this.endpoint,
        proxy  = this._proxyUri,
        target = this._tunnel ? uri : (proxy || uri);

    var params = {
      method:   'POST',
      host:     target.hostname,
      port:     target.port || this.DEFAULT_PORTS[target.protocol],
      path:     uri.path,
      headers:  Faye.extend({
        'Content-Length': content.length,
        'Content-Type':   'application/json',
        'Host':           uri.host
      }, this._dispatcher.headers)
    };

    var cookie = this._getCookies();
    if (cookie !== '') params.headers['Cookie'] = cookie;

    if (this._tunnel) {
      params.agent = this._tunnel;
    } else if (this._endpointSecure) {
      Faye.extend(params, this._dispatcher.tls);
    } else if (proxy) {
      params.path = this.endpoint.href;
      Faye.extend(params, this._proxy.tls);
      if (proxy.auth)
        params.headers['Proxy-Authorization'] = new Buffer(proxy.auth, 'utf8').toString('base64');
    }

    return params;
  },

  _handleResponse: function(messages, response) {
    var replies = null,
        body    = '',
        self    = this;

    response.setEncoding('utf8');
    response.on('data', function(chunk) { body += chunk });

    response.on('end', function() {
      try {
        replies = JSON.parse(body);
      } catch (e) {}

      if (replies)
        self._receive(replies);
      else
        self._handleError(messages);
    });
  }

}), {
  isUsable: function(dispatcher, endpoint, callback, context) {
    callback.call(context, Faye.URI.isURI(endpoint));
  }
});

Faye.Transport.register('long-polling', Faye.Transport.NodeHttp);

var crypto = require('crypto'),
    fs     = require('fs'),
    http   = require('http'),
    https  = require('https'),
    net    = require('net'),
    path   = require('path'),
    tls    = require('tls'),
    url    = require('url'),
    querystring = require('querystring'),

    csprng = require('csprng'),
    tunnel = require('tunnel-agent');

Faye.WebSocket   = require('faye-websocket');
Faye.EventSource = Faye.WebSocket.EventSource;
Faye.Cookies     = require('tough-cookie');

Faye.NodeAdapter = Faye.Class({
  DEFAULT_ENDPOINT: '/bayeux',
  SCRIPT_PATH:      'faye-browser-min.js',

  TYPE_JSON:    {'Content-Type': 'application/json; charset=utf-8'},
  TYPE_SCRIPT:  {'Content-Type': 'text/javascript; charset=utf-8'},
  TYPE_TEXT:    {'Content-Type': 'text/plain; charset=utf-8'},

  VALID_JSONP_CALLBACK: /^[a-z_\$][a-z0-9_\$]*(\.[a-z_\$][a-z0-9_\$]*)*$/i,

  initialize: function(options) {
    this._options = options || {};
    Faye.WebSocket.validateOptions(this._options, ['engine', 'mount', 'ping', 'timeout', 'extensions', 'websocketExtensions']);

    this._extensions = [];
    this._endpoint   = this._options.mount || this.DEFAULT_ENDPOINT;
    this._endpointRe = new RegExp('^' + this._endpoint.replace(/\/$/, '') + '(/[^/]*)*(\\.[^\\.]+)?$');
    this._server     = new Faye.Server(this._options);

    this._static = new Faye.StaticServer(path.dirname(__filename) + '/../browser', /\.(?:js|map)$/);
    this._static.map(path.basename(this._endpoint) + '.js', this.SCRIPT_PATH);
    this._static.map('client.js', this.SCRIPT_PATH);

    var extensions = this._options.extensions,
        websocketExtensions = this._options.websocketExtensions,
        i, n;

    if (extensions) {
      extensions = [].concat(extensions);
      for (i = 0, n = extensions.length; i < n; i++)
        this.addExtension(extensions[i]);
    }

    if (websocketExtensions) {
      websocketExtensions = [].concat(websocketExtensions);
      for (i = 0, n = websocketExtensions.length; i < n; i++)
        this.addWebsocketExtension(websocketExtensions[i]);
    }
  },

  listen: function() {
    throw new Error('The listen() method is deprecated - use the attach() method to bind Faye to an http.Server');
  },

  addExtension: function(extension) {
    return this._server.addExtension(extension);
  },

  removeExtension: function(extension) {
    return this._server.removeExtension(extension);
  },

  addWebsocketExtension: function(extension) {
    this._extensions.push(extension);
  },

  close: function() {
    return this._server.close();
  },

  getClient: function() {
    return this._client = this._client || new Faye.Client(this._server);
  },

  attach: function(httpServer) {
    this._overrideListeners(httpServer, 'request', 'handle');
    this._overrideListeners(httpServer, 'upgrade', 'handleUpgrade');
  },

  _overrideListeners: function(httpServer, event, method) {
    var listeners = httpServer.listeners(event),
        self      = this;

    httpServer.removeAllListeners(event);

    httpServer.on(event, function(request) {
      if (self.check(request)) return self[method].apply(self, arguments);

      for (var i = 0, n = listeners.length; i < n; i++)
        listeners[i].apply(this, arguments);
    });
  },

  check: function(request) {
    var path = url.parse(request.url, true).pathname;
    return !!this._endpointRe.test(path);
  },

  handle: function(request, response) {
    var requestUrl    = url.parse(request.url, true),
        requestMethod = request.method,
        self          = this;

    request.originalUrl = request.url;

    request.on('error', function(error) { self._returnError(response, error) });
    response.on('error', function(error) { self._returnError(null, error) });

    if (this._static.test(requestUrl.pathname))
      return this._static.call(request, response);

    // http://groups.google.com/group/faye-users/browse_thread/thread/4a01bb7d25d3636a
    if (requestMethod === 'OPTIONS' || request.headers['access-control-request-method'] === 'POST')
      return this._handleOptions(response);

    if (Faye.EventSource.isEventSource(request))
      return this.handleEventSource(request, response);

    if (requestMethod === 'GET')
      return this._callWithParams(request, response, requestUrl.query);

    if (requestMethod === 'POST')
      return this._concatStream(request, function(data) {
        var type   = (request.headers['content-type'] || '').split(';')[0],
            params = (type === 'application/json')
                   ? {message: data}
                   : querystring.parse(data);

        request.body = data;
        this._callWithParams(request, response, params);
      }, this);

    this._returnError(response, {message: 'Unrecognized request type'});
  },

  _callWithParams: function(request, response, params) {
    if (!params.message)
      return this._returnError(response, {message: 'Received request with no message: ' + this._formatRequest(request)});

    try {
      this.debug('Received message via HTTP ' + request.method + ': ?', params.message);

      var message = JSON.parse(params.message),
          jsonp   = params.jsonp || Faye.JSONP_CALLBACK,
          isGet   = (request.method === 'GET'),
          type    = isGet ? this.TYPE_SCRIPT : this.TYPE_JSON,
          headers = Faye.extend({}, type),
          origin  = request.headers.origin;

      if (!this.VALID_JSONP_CALLBACK.test(jsonp))
        return this._returnError(response, {message: 'Invalid JSON-P callback: ' + jsonp});

      if (origin) headers['Access-Control-Allow-Origin'] = origin;
      headers['Cache-Control'] = 'no-cache, no-store';
      headers['X-Content-Type-Options'] = 'nosniff';

      this._server.process(message, request, function(replies) {
        var body = Faye.toJSON(replies);

        if (isGet) {
          body = '/**/' + jsonp + '(' + this._jsonpEscape(body) + ');';
          headers['Content-Disposition'] = 'attachment; filename=f.txt';
        }

        headers['Content-Length'] = new Buffer(body, 'utf8').length.toString();
        headers['Connection'] = 'close';

        this.debug('HTTP response: ?', body);
        response.writeHead(200, headers);
        response.end(body);
      }, this);
    } catch (error) {
      this._returnError(response, error);
    }
  },

  _jsonpEscape: function(json) {
    return json.replace(/\u2028/g, '\\u2028').replace(/\u2029/g, '\\u2029');
  },

  handleUpgrade: function(request, socket, head) {
    var options  = {extensions: this._extensions, ping: this._options.ping},
        ws       = new Faye.WebSocket(request, socket, head, [], options),
        clientId = null,
        self     = this;

    request.originalUrl = request.url;

    ws.onmessage = function(event) {
      try {
        self.debug('Received message via WebSocket[' + ws.version + ']: ?', event.data);

        var message = JSON.parse(event.data),
            cid     = Faye.clientIdFromMessages(message);

        if (clientId && cid && cid !== clientId) self._server.closeSocket(clientId, false);
        self._server.openSocket(cid, ws, request);
        if (cid) clientId = cid;

        self._server.process(message, request, function(replies) {
          if (ws) ws.send(Faye.toJSON(replies));
        });
      } catch (e) {
        self.error(e.message + '\nBacktrace:\n' + e.stack);
      }
    };

    ws.onclose = function(event) {
      self._server.closeSocket(clientId);
      ws = null;
    };
  },

  handleEventSource: function(request, response) {
    var es       = new Faye.EventSource(request, response, {ping: this._options.ping}),
        clientId = es.url.split('/').pop(),
        self     = this;

    this.debug('Opened EventSource connection for ?', clientId);
    this._server.openSocket(clientId, es, request);

    es.onclose = function(event) {
      self._server.closeSocket(clientId);
      es = null;
    };
  },

  _handleOptions: function(response) {
    var headers = {
      'Access-Control-Allow-Credentials': 'false',
      'Access-Control-Allow-Headers':     'Accept, Content-Type, Pragma, X-Requested-With',
      'Access-Control-Allow-Methods':     'POST, GET, PUT, DELETE, OPTIONS',
      'Access-Control-Allow-Origin':      '*',
      'Access-Control-Max-Age':           '86400'
    };
    response.writeHead(200, headers);
    response.end('');
  },

  _concatStream: function(stream, callback, context) {
    var chunks = [],
        length = 0;

    stream.on('data', function(chunk) {
      chunks.push(chunk);
      length += chunk.length;
    });

    stream.on('end', function() {
      var buffer = new Buffer(length),
          offset = 0;

      for (var i = 0, n = chunks.length; i < n; i++) {
        chunks[i].copy(buffer, offset);
        offset += chunks[i].length;
      }
      callback.call(context, buffer.toString('utf8'));
    });
  },

  _formatRequest: function(request) {
    var method = request.method.toUpperCase(),
        string = 'curl -X ' + method;

    string += " 'http://" + request.headers.host + request.url + "'";
    if (method === 'POST') {
      string += " -H 'Content-Type: " + request.headers['content-type'] + "'";
      string += " -d '" + request.body + "'";
    }
    return string;
  },

  _returnError: function(response, error) {
    var message = error.message;
    if (error.stack) message += '\nBacktrace:\n' + error.stack;
    this.error(message);

    if (!response) return;

    response.writeHead(400, this.TYPE_TEXT);
    response.end('Bad request');
  }
});

for (var method in Faye.Publisher) (function(method) {
  Faye.NodeAdapter.prototype[method] = function() {
    return this._server._engine[method].apply(this._server._engine, arguments);
  };
})(method);

Faye.extend(Faye.NodeAdapter.prototype, Faye.Logging);

Faye.StaticServer = Faye.Class({
  initialize: function(directory, pathRegex) {
    this._directory = directory;
    this._pathRegex = pathRegex;
    this._pathMap   = {};
    this._index     = {};
  },

  map: function(requestPath, filename) {
    this._pathMap[requestPath] = filename;
  },

  test: function(pathname) {
    return this._pathRegex.test(pathname);
  },

  call: function(request, response) {
    var pathname = url.parse(request.url, true).pathname,
        filename = path.basename(pathname);

    filename = this._pathMap[filename] || filename;
    this._index[filename] = this._index[filename] || {};

    var cache    = this._index[filename],
        fullpath = path.join(this._directory, filename);

    try {
      cache.content = cache.content || fs.readFileSync(fullpath);
      cache.digest  = cache.digest  || crypto.createHash('sha1').update(cache.content).digest('hex');
      cache.mtime   = cache.mtime   || fs.statSync(fullpath).mtime;
    } catch (e) {
      response.writeHead(404, {});
      return response.end();
    }

    var type = /\.js$/.test(pathname) ? 'TYPE_SCRIPT' : 'TYPE_JSON',
        ims  = request.headers['if-modified-since'];

    var headers = {
      'ETag':          cache.digest,
      'Last-Modified': cache.mtime.toGMTString()
    };

    if (request.headers['if-none-match'] === cache.digest) {
      response.writeHead(304, headers);
      response.end();
    }
    else if (ims && cache.mtime <= new Date(ims)) {
      response.writeHead(304, headers);
      response.end();
    }
    else {
      headers['Content-Length'] = cache.content.length;
      Faye.extend(headers, Faye.NodeAdapter.prototype[type]);
      response.writeHead(200, headers);
      response.end(cache.content);
    }
  }
});

})();