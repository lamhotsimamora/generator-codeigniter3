function ready(callback){
    window.addEventListener("DOMContentLoaded", function(){
        callback();
    });
}

function log(message) {
    console.warn('[Vony-JS] ' + message)
}

function reload(u){
   var u = u ? u :''; 
   window.location.href=u;
}

function randomString(l) {
    l = (l === undefined) ? l = 10 : l;
    var r = '',
        c = 'a1b2c3d4e5f6g7h8i9j0kAlBmCnDoEpFqGrHsItJuKvLwMxNyOzPQRSTUVWXYZ';
    for (var i = 0; i < l; i++) {
        r += c.charAt(Math.floor(Math.random() * c.length))
    }
    return r
}

function isEmail(e) {
    var em = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return (e === undefined) ? !1 : em.test(e)
}

function isUrl(s) {
    var x = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return x.test(s)
}

function isFunction(f) {
    var t = {};
    return f && t.toString.call(f) === '[object Function]'
}

function whatThis(o) {
    return isUndefined(o) ? undefined : typeof o
}

function isUndefined(o) {
    return (o === undefined) ? !0 : !1
}

function moneyFormat(v, r) {
    r = r === undefined ? 'Rp ' : r;
    return r + v.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

function validateInput(){
    for (var i = 0; i < arguments.length; ++i) {
        if (arguments[i].get().trim() === '' || arguments[i].get().trim() == null) {
            arguments[i].focus();
            return false;
            break;
        }
    }
    return true;
}

class _vn_ {
    constructor(data) {
        if (data) {
            this.id = data.id ? data.id : null;
            this.url =data.url ? data.url :null;
            this.method =data.method ? data.method :null;
            this.data =data.data ? data.data :null;
            this.obj = null;
            this.title = data.title ? data.title : null;
            if (this.title!=null){
                this.makeTitle(this.title);
            }
            this.urlRoute = data.urlRoute ? data.urlRoute :null;
            this.element = null;
            this.documentGetElementById();
            if (data.focus && data.focus == true) {
                if (this.element === 'INPUT' || this.element === 'TEXTAREA') {
                    this.obj.focus();
                }
            }
        }
        return this;
    }

    focus() {
        if (this.obj) {
            if (this.element === 'INPUT' || this.element === 'TEXTAREA') {
                this.obj.focus()
            }
        }
    }

    documentGetElementById() {
        this.obj = document.getElementById(this.id);
        if (this.obj) {
            this.element = this.obj.tagName;
            log('ID of element is ' + this.id + ' , component is ' + this.element)
        }
    }

    hide() {
        if (this.obj) {
            this.obj.style.visibility = 'hidden'
        }
        return this;
    }

    show() {
        if (this.obj) {
            this.obj.removeAttribute('style');
        }
        return this;
    }

    get(attribute) {
        if (this.obj) {
            if (attribute){
                return this.obj.getAttribute(attribute);
            }
            if (this.element === 'INPUT' || this.element === 'TEXTAREA') {
                return this.obj.value;
            } else if (this.element === 'IMG') {
                return this.obj.src;
            } else if (this.element === 'SELECT') {
                return this.obj.value;
            } else {
                return this.obj.innerHTML;
            }
        }
        return this;
    }

    clear(){
        this.set('');
        return this;
    }

    set(text) {
        if (this.obj) {
            if (this.element === 'INPUT' || this.element === 'TEXTAREA') {
                this.obj.value = text;
            } else if (this.element === 'IMG') {
                this.obj.src = text;
            } else if (this.element === 'SELECT') {
                this.obj.selectedIndex = text
            } else {
                this.obj.innerHTML = text;
            }
        }
        return this;
    }

    on(type, callback) {
        if (this.obj) {
            var $this = this;
            this.obj.addEventListener(type, function () {
                callback($this);
            })
        }
        return this
    }

    ajax(callback){
        var _data_=null;
        if (this.method==null){
            this.method='get';
        }
        var _METHOD_ = this.method.toLowerCase();
        if (this.url==null){
            log("URL of ajax is Null, Ajax Stop");
            return;
        }
        var _URL_ = this.url;

        if (_METHOD_==='post'){
            var i = 0;
            for (var key in this.data) {
                if (key === 'length' || !this.data.hasOwnProperty(key)) {
                    continue
                }
                var vL = this.data[key];
                (i == 0) ? _data_ = key + '=' + vL: _data_ += '&' + key + '=' + vL;
                i++
            }
        }
        log("Method of ajax is "+_METHOD_);
        log("URL of ajax is "+_URL_);
    
        var x = XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHttp');
        x.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                callback(this.responseText);
            }
        };
        x.onerror = function () {
           
        };

        x.open(_METHOD_, _URL_, !0);
        var header = 'application/x-www-form-urlencoded';
        x.setRequestHeader('Content-Type', header);
        try {
            x.send(_data_)
        } catch (e) {
            log('Error ajax - '+e);
        }
        return this
    }

    change(key,val){
        if (this.obj){
            this.obj.setAttribute(key,val);
        }
    }
    
    enabled(v){
        if (this.obj){
            this.obj.enabled =true;
        }
        return this
    }

    disabled(){
        if (this.obj){
            this.obj.disabled =true;
        }
        return this
    }

    makeTitle(value){
        document.title = value;
    }

    route(callback=null,title=null,urlRoute=null){
        let finalTitle,finalUrlPath;
        finalTitle =  (title!=null) ? title : this.title;
        finalUrlPath =  (urlRoute!=null) ? urlRoute : this.urlRoute;
        finalUrlPath = finalUrlPath.toLowerCase();

        window.history.pushState({finalUrlPath},finalTitle,finalUrlPath);
        log("URL changed -> "+finalUrlPath);
        callback==null ? null : callback();
        window.onpopstate = (event) => {
            log(`location: ${document.location}, state: ${JSON.stringify(event.state)}`)
        }
        return this
    }

}


function Vony(d) {
    return new _vn_(d);
}
