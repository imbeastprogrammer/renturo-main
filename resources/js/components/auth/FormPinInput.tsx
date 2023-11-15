import ReactPinInput from 'react-pin-input';

type PinInputProps = {
    length: number;
    value: string;
    secret?: boolean;
    onChange: (value: string) => void;
    onComplete?: (value: string) => void;
};

function FormPinInput({
    secret = true,
    length,
    value,
    onChange,
    onComplete,
}: PinInputProps) {
    return (
        <ReactPinInput
            length={length}
            initialValue={value}
            {...(secret && { secret, secretDelay: 100 })}
            onChange={(value) => {
                onChange(value);
            }}
            type='numeric'
            inputMode='number'
            style={{
                padding: '10px',
                width: '100%',
                display: 'grid',
                columnGap: '1rem',
                gridTemplateColumns: `repeat(4,1fr)`,
            }}
            inputStyle={{
                borderLeftWidth: 0,
                borderRightWidth: 0,
                borderTopWidth: 0,
                borderBottomWidth: '2px',
                width: '100%',
                borderColor: 'black',
                fontSize: '32px',
                fontWeight: 'bold',
            }}
            inputFocusStyle={{ borderColor: '#185ADC' }}
            onComplete={(value) => onComplete && onComplete(value)}
            autoSelect={true}
            regexCriteria={/^[ A-Za-z0-9_@./#&+-]*$/}
        />
    );
}

export default FormPinInput;
