import _ from 'lodash';
import {
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Input } from '@/components/ui/input';
import { FormControl, FormItem, FormLabel } from '@/components/ui/form';
import { Switch } from '@/components/ui/switch';

import { FieldTypes, toolboxItems } from '../toolboxItems';
import { useController, useFieldArray } from 'react-hook-form';
import ChoicesGenerator from './ChoicesGenerator';
import { FormFields } from '../..';

type PropertyEditorProps = {
    index: number;
    item: FormFields;
};

const hasOptions = [
    FieldTypes.CHECKBOX,
    FieldTypes.CHECKLIST,
    FieldTypes.DROPDOWN,
    FieldTypes.RADIO_BUTTON,
];

function PropertyEditor({ index, item }: PropertyEditorProps) {
    const formLabelField = useController({
        name: `custom_fields.${index}.label`,
    });
    const fieldTypeField = useController({
        name: `custom_fields.${index}.type`,
    });
    const isRequiredField = useController({
        name: `custom_fields.${index}.is_required`,
    });

    const fieldTypes = _.flatMapDepth(toolboxItems.map(({ items }) => items));
    const fieldType = fieldTypes.find(
        ({ id }) => id === fieldTypeField.field.value,
    );

    const optionsFieldArray = useFieldArray({
        name: `custom_fields.${index}.options`,
    });

    return (
        <AccordionItem key={item.id} className='border-none' value={item.id}>
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
                                    checked={isRequiredField.field.value}
                                    onCheckedChange={() =>
                                        isRequiredField.field.onChange(
                                            !isRequiredField.field.value,
                                        )
                                    }
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
                                    value={formLabelField.field.value}
                                    className='outline-none focus-visible:ring-transparent'
                                    onChange={(e) =>
                                        formLabelField.field.onChange(
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormControl>
                        </FormItem>
                    </div>
                    {fieldType?.id && hasOptions.includes(fieldType?.id) && (
                        <ChoicesGenerator
                            index={index}
                            choices={optionsFieldArray.fields as any}
                            onRemove={(i) => optionsFieldArray.remove(i)}
                            onAppend={() =>
                                optionsFieldArray.append({ value: 'option' })
                            }
                        />
                    )}
                </div>
            </AccordionContent>
        </AccordionItem>
    );
}

export default PropertyEditor;
