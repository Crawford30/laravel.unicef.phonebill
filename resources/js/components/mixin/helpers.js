const Helpers = {
    methods: {
        numerilize(n, t, d){
            if(typeof t === undefined || t == null){
                if(typeof n === 'number' || typeof n === 'string'){
                    if(n > 1000000000000) return (n / 1000000000000).toFixed(d) + " Tn";
                    else if(n > 1000000000) return (n / 1000000000).toFixed(d) + " Bn";
                    else if(n > 1000000) return (n / 1000000).toFixed(d) + " M";
                    else if(n > 1000) return (n / 1000).toFixed(d) + " K";
                    return n;
                } else { return n; }
            } else {
                var num = !isFinite(+n) ? 0 : +n, 
                    prec = !isFinite(+d) ? 0 : Math.abs(d),
                    sep = ",",
                    dec = ".",
                    toFixedFix = function (num, prec) {
                        var k = Math.pow(10, prec);
                        return Math.round(num * k) / k;
                    },
                    s = (prec ? toFixedFix(num, prec) : Math.round(num)).toString().split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
        }
    }
}

export default Helpers;