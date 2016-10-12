var forms = document.querySelectorAll('div.event--vote-form form');
var starContainers = document.querySelectorAll('.stars');

var lockForm = function (form) {
    form.querySelector('button[type=submit]').disabled = true; //setAttribute('disabled', 'disabled');
}

var unlockForm = function (form) {
    form.querySelector('button[type=submit]').disabled = false; //removeAttribute('disabled');
}

var resetFormError = function (form) {
    var errors = form.querySelector('div.errors');
    if (errors !== null) {
        errors.remove();
    }
    form.querySelector('button[type=submit]').classList.remove('error');
    form.querySelector('button[type=submit]').classList.remove('success');
};

var setFormErrors = function (form, errors) {
    var submit = form.querySelector('button[type=submit]');
    submit.classList.add('error');
    var div = document.createElement('div');
    div.setAttribute('class', 'errors');
    var content = document.createElement('p');
    content.textContent = 'Ooooops ! Quelque chose s\'est mal passé !';
    var ul = document.createElement('ul');
    var li = document.createElement('li');
    for (var error in errors) {
        if (errors.hasOwnProperty(error)) {
            var myLi = li.cloneNode();
            myLi.textContent = errors[error];
            ul.appendChild(myLi);
        }
    }
    div.appendChild(content);
    div.appendChild(ul);
    submit.parentNode.appendChild(div);
};

var setFormSuccess = function(form) {
    form.querySelector('button[type=submit]').classList.add('success');
};

[].forEach.call(forms, function (form) {
    var starContainer = form.querySelector('.stars');

    form.addEventListener('submit', function(e){
        e.preventDefault();
        e.stopPropagation();

        lockForm(this);
        resetFormError(this);

        var form = this;

        var httpRequest = new XMLHttpRequest()
        httpRequest.onreadystatechange = function (data) {
            var DONE = 4; // readyState 4 means the request is done.
            var OK = 200; // status 200 is a successful return.
            var ACCESS_DENIED = 403;
            var BAD_REQUEST = 400;
            var SERVER_ERROR = 500;

            if (httpRequest.readyState === DONE) {
                unlockForm(form);

                var errors = {};
                var errorMessage = '';
                if (httpRequest.getResponseHeader('content-type') === 'application/json') {
                    errors = JSON.parse(httpRequest.responseText);
                    if (httpRequest.status === OK) {
                        setFormSuccess(form);
                        return ;
                    } else if (httpRequest.status !== ACCESS_DENIED && httpRequest.status !== BAD_REQUEST && httpRequest.status !== SERVER_ERROR) {
                        errors = ['Une erreur inattendue est survenue :\'('];
                    }
                } else {
                    errors = ['Une erreur inattendue est survenue :\'('];
                }

                setFormErrors(form, errors, errorMessage);
            }
        };
        httpRequest.open('POST', this.getAttribute('action'));
        httpRequest.setRequestHeader('Accept', 'application/json');

        var data = new FormData(form);
        httpRequest.send(data);
    });

    starContainer.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var stars = Array.prototype.slice.call(this.children);
        var totalStars = stars.length;

        var index = stars.indexOf(e.target);
        var vote = totalStars - index;

        stars.forEach(function(el) {
            el.classList.remove('is-selected')
        })
        e.target.classList.add('is-selected')
        var id = form.querySelector('input[name*="sessionId"]').getAttribute('value');
        form.querySelector('input[name="vote' + id + '[vote]"]').setAttribute('value', vote);
    });
});