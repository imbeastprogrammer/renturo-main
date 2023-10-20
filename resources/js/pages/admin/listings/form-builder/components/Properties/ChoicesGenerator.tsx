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

type ChoicesGeneratorProps = {
    choices: { id: string; value: string }[];
    onAppend: () => void;
    onRemove: (idx: number) => void;
};

function ChoicesGenerator({
    choices,
    onAppend,
    onRemove,
}: ChoicesGeneratorProps) {
    return (
        <div className='space-y-2 rounded-lg bg-white p-4'>
            <FormItem>
                <FormLabel className='text-gray-500'>Choices</FormLabel>
                <FormControl>
                    <Accordion type='single' collapsible className='space-y-2'>
                        {choices.map((option, i) => (
                            <AccordionItem
                                key={option.id}
                                value={option.id}
                                className='rounded-lg border-none bg-gray-100 px-4'
                            >
                                <AccordionTrigger>
                                    <p key={option.id}>{`Option ${i + 1}`}</p>
                                </AccordionTrigger>
                                <AccordionContent>
                                    <Input
                                        value={option.value}
                                        className='focus-visible:border-none focus-visible:ring-0 focus-visible:ring-transparent'
                                    />
                                    <div className='mt-2 flex justify-end'>
                                        <TrashIcon
                                            className='h-4 w-4 cursor-pointer text-red-500'
                                            onClick={() => onRemove(i)}
                                        />
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
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

export default ChoicesGenerator;
