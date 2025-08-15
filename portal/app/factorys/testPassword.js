app

.factory('TestPassword', function() {
    function _analyzeRepeatedChar(password) {
        var amountTotal = 0,
            character = [];

        for(i=0; i<password.length; i++) {
            if(character.indexOf(password.charAt(i)) === -1){
                character.push(password.charAt(i));
            } else {
                amountTotal++;
            }
        }

        return amountTotal;
    }

    function _addPoint(password) {
        var letterLowercase = password.match(/[a-z]{1,}/g) || [];
        var letterUppercase = password.match(/[A-Z]{1,}/g) || [];
        var number = password.match(/\d{1,}/g) || [];
        var specialChar = password.match(/\W{1,}/g) || [];
        var amoutLetterLowercase = 0;
        var amoutLetterUppercase = 0;
        var amoutNumber = 0;
        var amoutSpecialChar = 0;
        var power = 0;

        letterLowercase
            .forEach(function(value) {
                amoutLetterLowercase += value.length;
            });

        letterUppercase
            .forEach(function(value) {
                amoutLetterUppercase += value.length;
            });

        number
            .forEach(function(value) {
                amoutNumber += value.length;
            });

        specialChar
            .forEach(function(value) {
                amoutSpecialChar += value.length;
            });

        if ((password.length >= 8) && (password.length < 10))  {
            power += 10;
        } else if ((password.length >= 10) && (password.length <= 12)) {
            power += 15;
        } else if ((password.length >= 13) && (password.length <= 15)) {
            power += 20;
        } else if (password.length > 15) {
            power += 25;
        }

        if (amoutLetterLowercase >= 3) {
            power += 17;
        } else if (amoutLetterLowercase >= 1) {
            power += 10;
        }

        if (amoutLetterUppercase >= 3) {
            power += 18;
        } else if (amoutLetterUppercase >= 1) {
            power += 10;
        }

        if (amoutNumber >= 3) {
            power += 18;
        } else if (amoutNumber >= 1) {
            power += 10;
        }

        if (amoutSpecialChar >= 6) {
            power += 40;
        } else if (amoutSpecialChar >= 5) {
            power += 30;
        } else if (amoutSpecialChar >= 4) {
            power += 25;
        } else if (amoutSpecialChar >= 3) {
            power += 20;
        } else if (amoutSpecialChar >= 2) {
            power += 15;
        } else if (amoutSpecialChar == 1) {
            power += 10;
        }

        return power;
    }

    function _subtractPoint(password, param) {
        var power = 0,
            sequentialPasswords = /^(ASDFGHJKLÇ|asdfghjklç)$/;
        param = Array.isArray(param) ? param : [];

        if (password.match(sequentialPasswords)) {
            power += 35;
        }

        if (password.length < 7) {
            power += 5;
        }

        if (password.length >= 6 && password.match(/^[0-9]+$/)) {
            power += 35;
        }

        if (password.length >= 6 && password.match(/^[a-z]+$/)) {
            power += 35;
        }

        if (password.length >= 6 && password.match(/^[A-Z]+$/)) {
            power += 30;
        }

        param
            .forEach(function(value) {
                var regex = new RegExp(value,"i");

                if (regex.test(regex)) {
                    power += 50;
                }
            });

        amountConsecutiveChar = password.match(/([a-zA-z0-9\s\W])\1{1,}/g) || [];
        repeatedCharTotal = _analyzeRepeatedChar(password) || 0;

        power += parseInt(amountConsecutiveChar.length) * 5;
        power += repeatedCharTotal * 2;

        return power;
    }

    function _check(password, user, name){
        var power = 0;

        power += _addPoint(password);
        power -= _subtractPoint(password);

        return power;
    }

    return {
        check: _check
    };
});