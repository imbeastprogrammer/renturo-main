import ReactPinInput from "react-pin-input";

type PinInputProps = {
    length: number;
    value: string;
    onChange: (value: string) => void;
    onComplete?: (value: string) => void;
};

function PinInput({ length, value, onChange, onComplete }: PinInputProps) {
    return (
        <ReactPinInput
            length={length}
            secret
            initialValue={value}
            secretDelay={100}
            onChange={(value) => {
                onChange(value);
            }}
            type="numeric"
            inputMode="number"
            style={{
                padding: "10px",
                width: "100%",
                display: "grid",
                gridTemplateColumns: `repeat(4,1fr)`,
            }}
            inputStyle={{
                borderWidth: "2px",
                width: "80px",
                height: "80px",
                borderRadius: "1rem",
            }}
            inputFocusStyle={{ borderColor: "#185ADC" }}
            onComplete={(value) => onComplete && onComplete(value)}
            autoSelect={true}
            regexCriteria={/^[ A-Za-z0-9_@./#&+-]*$/}
        />
    );
}

export default PinInput;
