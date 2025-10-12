import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { PropsWithChildren } from 'react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { Control, FieldValues, Path } from 'react-hook-form';

type FormTextAreaProps<T> = {
    label?: string;
    control: Control<FieldValues & T>;
    name: Path<FieldValues & T>;
    description?: string;
    data: { label: string; value: string; image: string }[];
};

function FormListingSelector<T>({
    label,
    control,
    name,
    description,
    data,
}: FormTextAreaProps<T>) {
    return (
        <FormField
            control={control}
            name={name}
            render={({ field }) => {
                const selected = data.find(
                    ({ value }) => value === field.value,
                );
                return (
                    <FormItem>
                        <FormLabel className='text-xl font-medium'>
                            Your Listings
                        </FormLabel>
                        <FormDescription className='font-medium text-black/50'>
                            {description}
                        </FormDescription>
                        <FormControl>
                            <Select
                                value={field.value}
                                onValueChange={field.onChange}
                            >
                                <SelectTrigger className='h-auto min-h-[53px] bg-[#F3F7FD] text-lg font-medium focus-visible:ring-transparent'>
                                    <SelectValue placeholder='Select Listing'>
                                        <SelectorTrigger
                                            image={selected?.image}
                                        >
                                            {selected?.label}
                                        </SelectorTrigger>
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    {data.map(({ value, image, label }) => (
                                        <SelectItem
                                            key={value}
                                            value={value}
                                            className='h-max w-full'
                                        >
                                            <SelectionItem image={image}>
                                                {label}
                                            </SelectionItem>
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                );
            }}
        />
    );
}

type SelectTriggerProps = {
    image?: string;
} & PropsWithChildren;

function SelectorTrigger({ image, children }: SelectTriggerProps) {
    return (
        <div className='flex items-center gap-4'>
            <img
                src={image}
                alt='listing image'
                style={{ opacity: image ? 1 : 0 }}
                className='object-conver h-[53px] w-[53px] flex-shrink-0 rounded-lg'
            />
            <h1 className='text-lg font-medium'>{children}</h1>
        </div>
    );
}

type SelectionItemProps = {
    image: string;
} & PropsWithChildren;

function SelectionItem({ image, children }: SelectionItemProps) {
    return (
        <div className='flex cursor-pointer items-center gap-4 rounded-lg'>
            <img
                src={image}
                alt='listing image'
                className='h-[53px] w-[53px] flex-shrink-0 rounded-lg object-cover'
            />
            <h1 className='text-lg font-medium leading-none'>{children}</h1>
        </div>
    );
}

export default FormListingSelector;
