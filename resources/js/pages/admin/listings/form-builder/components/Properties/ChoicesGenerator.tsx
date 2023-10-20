import { TrashIcon } from 'lucide-react';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { FormControl, FormItem, FormLabel } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { useController } from 'react-hook-form';

type ChoicesGeneratorProps = {
    index: number;
    choices: { id: string; value: string }[];
    onAppend: () => void;
    onRemove: (idx: number) => void;
};

function ChoicesGenerator({
    choices,
    onAppend,
    onRemove,
    index,
}: ChoicesGeneratorProps) {
    const multipleOptionsFieldArray = useController({
        name: `custom_fields.${index}.allow_multiple_option_answer`,
    });
    return (
        <div className='space-y-2 rounded-lg bg-white p-4'>
            <FormItem>
                <FormLabel className='text-gray-500'>Choices</FormLabel>
                <div>
                    <Label className='mb-4 flex items-center gap-2 font-normal text-gray-500'>
                        <Checkbox
                            checked={multipleOptionsFieldArray.field.value}
                            onCheckedChange={() =>
                                multipleOptionsFieldArray.field.onChange(
                                    !multipleOptionsFieldArray.field.value,
                                )
                            }
                            className='data-[state=checked]:bg-metalic-blue'
                        />
                        Multple answers accepted
                    </Label>
                </div>
                <FormControl>
                    <Accordion type='single' collapsible className='space-y-2'>
                        {choices.map((option, i) => (
                            <ChoiceItem
                                option={option}
                                nestedIndex={index}
                                currentIndex={i}
                                onRemove={() => onRemove(i)}
                            />
                        ))}
                    </Accordion>
                </FormControl>
            </FormItem>
            <Button
                size='sm'
                className='h-6 bg-metalic-blue hover:bg-metalic-blue/90'
                onClick={onAppend}
            >
                Add Option
            </Button>
        </div>
    );
}

type ChoiceItemProps = {
    option: { id: string; value: string };
    nestedIndex: number;
    currentIndex: number;
    onRemove: () => void;
};

function ChoiceItem({
    option,
    nestedIndex,
    currentIndex,
    onRemove,
}: ChoiceItemProps) {
    const optionField = useController({
        name: `custom_fields.${nestedIndex}.options.${currentIndex}.value`,
    });
    return (
        <AccordionItem
            key={option.id}
            value={option.id}
            className='rounded-lg border-none bg-gray-100 px-4'
        >
            <AccordionTrigger>{`Option ${currentIndex + 1}`}</AccordionTrigger>
            <AccordionContent>
                <Input
                    value={optionField.field.value}
                    onChange={(e) => optionField.field.onChange(e.target.value)}
                    className='focus-visible:border-none focus-visible:ring-0 focus-visible:ring-transparent'
                />
                <div className='mt-2 flex justify-end'>
                    <TrashIcon
                        className='h-4 w-4 cursor-pointer text-red-500'
                        onClick={onRemove}
                    />
                </div>
            </AccordionContent>
        </AccordionItem>
    );
}

export default ChoicesGenerator;
