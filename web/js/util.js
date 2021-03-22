Inputmask.extendAliases({
    cuit: {
        mask: "aa-9{8}-c",
        definitions: {
            'a': {
                validator: "[20|23|24|27|30|33|34]",
            },
            'c': {
                validator: function(chrs, maskset, pos, strict, opts) {
                    return Cuit.validar(maskset.buffer.join('').replace(/_|-/g,"") + chrs);
                },
                cardinality: 1
            }
        },
        onUnMask: function(maskedValue, unmaskedValue, opts) {
            return maskedValue;
        },
        inputmode: "numeric"
    }
});


Cuit = new function() {
    var validar = function(cuit) {
        cuit = cuit.replace(/-/g, "");
        var aMult = '5432765432';
        var aMult = aMult.split('');

        if (cuit && cuit.length == 11)
        {
            aCUIT = cuit.split('');
            var iResult = 0;
            for(i = 0; i <= 9; i++)
            {
                iResult += aCUIT[i] * aMult[i];
            }
            iResult = (iResult % 11);
            iResult = 11 - iResult;

            if (iResult == 11) iResult = 0;
            if (iResult == 10) iResult = 9;

            if (iResult == aCUIT[10])
            {
                return true;
            }
        }
        return false;
    }

    return {
        validar: validar,
    }
};