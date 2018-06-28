(function() {

    window.cx || (window.cx = {});

    cx.Math = {
        combine: function(n, m) {
            var dividend = this.factorial(n, n - m + 1);
            var divisor = this.factorial(m);

            return dividend / divisor;
        },
        harmonic: function() {
            var args = [].slice.call(arguments, 0);
            var sum = 0;
            for (var i = 0; i < args.length; ++i) {
                sum += 1 / args[i];
            }
            return 1 / sum;
        },
        slowCombineList: function(arr, n, z) { // z is max count
            n = parseInt(n, 10);
            z = parseInt(z, 10);
            var r = [];
            fn([], arr, n);
            return r;
            function fn(t, a, n) {
                if (n === 0 || z && r.length == z) {
                    return r[r.length] = t;
                }
                for (var i = 0, l = a.length - n; i <= l; i++) {
                    if (!z || r.length < z) {
                        var b = t.slice();
                        b.push(a[i]);
                        fn(b, a.slice(i + 1), n - 1);
                    }
                }
            }
        },
        combineList: function(arr, ns, cb) {
            var len = arr.length;
            var pow = Math.pow(2, len);
            var result = {};
            for (var k = 0; k < ns.length; ++k) {
                result[ns[k]] = [];
            }
            var tmp;
            var n;
            for (var i = 0; i < pow; ++i) {
                for (var j = 0; j < ns.length; ++j) {
                    tmp = [];
                    n = ns[j];
                    if (cx.Math.bitCount(i) == n) {
                        for (var j = 0; j < len; ++j) {
                            if ((i & (1 << j)) != 0) {
                                tmp.push(arr[j]);
                            }
                        }
                    }
                    if (tmp.length > 0) {
                        if ($.isFunction(cb)) {
                            result[n].push(cb(tmp));
                        } else {
                            result[n].push(tmp);
                        }
                    }
                }
            }
            return result;
        },
        variance: function(nums) {
            var average = 0;
            var variance = 0;
            for (var i = 0; i < nums.length; ++i) {
                average += parseFloat(nums[i], 10);
            }
            average /= nums.length;
            for (var i = 0; i < nums.length; ++i) {
                variance += (nums[i] - average) * (nums[i] - average);
            }
            return variance;
        },
        factorial: function(n, s) {
            if (n == 0) {
                return 1;
            }
            var product = 1;
            (s > 0) || (s = 1);
            for (var i = s; i <= n; ++i) {
                product *= i;
            }
            return product;
        },
        product: function(arr) {
            var p = 1;
            for (var i = 0, len = arr.length; i < len; ++i) {
                if ($.isArray(arr[i])) {
                    p *= arr[i].length;
                } else {
                    p *= arr[i];
                }
            }
            return len ? p : 0;
        },
        all: function (A2, fn){
            var n = 0, codes = [], code = [], isTest = $.isFunction(fn);
            function each(A2, n) {
                if(n >= A2.length) {
                    if(!isTest || false !== fn(code)) {
                        codes.push(code.slice());
                    }
                    code.length = n - 1;
                } else {
                    var cur = A2[n];
                    for(var i = 0, j = cur.length; i < j; i++) {
                        code.push(cur[i]);
                        each(A2, n + 1);
                    }
                    if(n) {
                        code.length = n - 1;
                    }
                }
            }
            if(A2.length) {
                each(A2, n);
            }
            return codes;
        },
        bitCount: function(i) {
            var count = 0;
            while (i) {
                count += 1;
                i &= (i - 1);
            }
            return count;
        },
        //四舍六入
        round2: function(i) {
        	var value = i * 10;
        	if (value % 10 != 5) {
        		return Math.round(i);
        	}else {
        		if (Math.floor(value / 10) % 2 == 1) {
        			return Math.ceil(i);
        		}else {
        			return Math.floor(i);
        		}
        	}
        },
        multiply: function(x, y) {
        	var a = x.toString().split('.');
        	var b = y.toString().split('.');
        	var al = x.toString().split('.')[1] ? x.toString().split('.')[1].length : 0;
        	var bl = y.toString().split('.')[1] ? y.toString().split('.')[1].length : 0;
        	var length = al-bl;
        	if (length < 0) {
        		x = parseInt(x.toString().replace('.', '') * Math.pow(10, Math.abs(length)), 10);
        		y = parseInt(y.toString().replace('.', ''), 10);
        	}else {
        		x = parseInt(x.toString().replace('.', ''), 10);
        		y = parseInt(y.toString().replace('.', '') * Math.pow(10, Math.abs(length)), 10);
        	}
        	return x * y / Math.pow(Math.pow(10, Math.max(al, bl)), 2)
        }
    };
})();
