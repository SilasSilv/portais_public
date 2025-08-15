app

.filter("Ucwords", function() {

    return function(str) {
        var words = str.split(" ");
        str = "";

        words.forEach(function(word) {
                word = word.toLowerCase();

                if (/^(da|de)$/.test(word.trim())){
                    str += word + " ";
                } else {
                    str += word.charAt(0).toUpperCase() + word.substr(1) + " ";
                }

            });

        return str;
    };

});