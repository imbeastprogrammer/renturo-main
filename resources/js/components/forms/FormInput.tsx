import { LucideIcon } from "lucide-react";
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "../ui/form";
import { Input, InputProps } from "../ui/input";
import { Control, FieldValues, Path } from "react-hook-form";

type FormInputProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    icon?: LucideIcon;
} & InputProps;

function FormInput<T>({
    control,
    name,
    icon: Icon,
    ...props
}: FormInputProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => (
                <FormItem className="w-full">
                    {props.label && <FormLabel>{props.label}</FormLabel>}
                    <FormControl>
                        <div className="flex gap-2 relative items-center">
                            <Input
                                className="p-6 pr-16 bg-light-carbon placeholder:text-gray-400 rounded-lg"
                                {...field}
                                {...props}
                            />
                            {Icon && (
                                <Icon className="absolute right-6 text-gray-400" />
                            )}
                        </div>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            )}
        />
    );
}

export default FormInput;
