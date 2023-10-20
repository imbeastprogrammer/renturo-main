import _ from 'lodash';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { FormFields } from '../..';
import { toolboxItems } from '../toolboxItems';
import { Switch } from '@/components/ui/switch';
import { Input } from '@/components/ui/input';
import { FormControl, FormItem, FormLabel } from '@/components/ui/form';

type PropertiesProps = {
    items: FormFields[];
};
function Properties({ items }: PropertiesProps) {
    const fieldTypes = _.flatMapDepth(toolboxItems.map(({ items }) => items));

    return (
        <div className='bg-[#f4f4f4] p-4'>
            <Accordion type='single' className='space-y-4' collapsible>
                {items.map((item) => {
                    const fieldType = fieldTypes.find(
                        ({ id }) => id === item.type,
                    );

                    return (
                        <AccordionItem
                            key={item.id}
                            className='border-none'
                            value={item.id}
                        >
                            <AccordionTrigger className='rounded-lg bg-white p-4'>
                                <div className='flex gap-3'>
                                    {fieldType?.icon && (
                                        <fieldType.icon className='text-metalic-blue' />
                                    )}
                                    {fieldType?.title}
                                </div>
                            </AccordionTrigger>
                            <AccordionContent>
                                <div className='mt-2 space-y-2'>
                                    <div className='rounded-lg bg-white p-2 px-4'>
                                        <FormItem className='flex items-center justify-between'>
                                            <FormLabel className='text-gray-500'>
                                                Required
                                            </FormLabel>
                                            <FormControl>
                                                <Switch
                                                    checked={item.is_required}
                                                />
                                            </FormControl>
                                        </FormItem>
                                    </div>
                                    <div className='rounded-lg bg-white p-4'>
                                        <FormItem>
                                            <FormLabel className='text-gray-500'>
                                                Text
                                            </FormLabel>
                                            <FormControl>
                                                <Input
                                                    value={item.label}
                                                    className='outline-none focus-visible:ring-transparent'
                                                />
                                            </FormControl>
                                        </FormItem>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    );
                })}
            </Accordion>
        </div>
    );
}

export default Properties;
