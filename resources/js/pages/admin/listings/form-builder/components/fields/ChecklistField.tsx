import { z } from 'zod';
import { TrashIcon } from 'lucide-react';
import {
    ElementsType,
    FormElement,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';

import useFormBuilder from '@/hooks/useFormBuilder';
import useFieldTypes from '../../hooks/useFieldTypes';
import FieldTypeChanger from '../FieldTypeChanger';
import PropertyEditorHandle from '../PropertyEditorHandle';

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
    options: [],
    multiple_answer_accepted: false,
};

const schema = z.object({
    is_required: z.boolean(),
    label: z.string(),
    options: z.array(z.string()).default([]),
    multiple_answer_accepted: z.boolean(),
});

const ChecklistField: FormElement = {
    type: 'checklist',
    construct: (id) => ({
        id,
        type: 'checklist',
        extraAttributes,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField, updateField, current_page_id } =
        useFormBuilder();
    const elementInstance = element as FormElementInstance & {
        extraAttributes: typeof extraAttributes;
    };

    const { currentFieldType, fieldTypes } = useFieldTypes(element.type);

    const handleValueChange = (value: ElementsType) => {
        updateField(
            current_page_id,
            element.id,
            FormElements[value].construct(element.id),
        );
    };

    return (
        <div className='w-full' onSelect={() => setSelectedField(element)}>
            <div className='flex justify-between'>
                <FieldTypeChanger
                    icon={currentFieldType?.icon}
                    value={currentFieldType?.id}
                    onValueChange={handleValueChange}
                    data={fieldTypes}
                />
                <TrashIcon
                    className='text-red-500'
                    onClick={() => removeField(current_page_id, element.id)}
                />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                {elementInstance.extraAttributes.options.map((option) => (
                    <div
                        key={option}
                        className='flex items-center gap-4 rounded-lg bg-metalic-blue/5 p-2 px-4 text-metalic-blue'
                    >
                        <Checkbox className='border-metalic-blue' />
                        <Label>{option}</Label>
                    </div>
                ))}
            </div>
        </div>
    );
}

type PropertiesComponentProps = {
    element: FormElementInstance;
};

function PropertiesComponent({ element }: PropertiesComponentProps) {
    const { updateField, current_page_id } = useFormBuilder();
    const form = useForm<z.infer<typeof schema>>({
        defaultValues: element.extraAttributes,
        resolver: zodResolver(schema),
    });

    const options = form.watch('options');

    const { currentFieldType } = useFieldTypes(element.type);

    const applyChanges = form.handleSubmit((values) => {
        updateField(current_page_id, element.id, {
            ...element,
            extraAttributes: { ...values },
        });
    });

    return (
        <AccordionItem value={element.id} className='border-0'>
            <AccordionTrigger className='mb-2 rounded-lg bg-white p-3 px-4'>
                {currentFieldType && (
                    <PropertyEditorHandle
                        type={currentFieldType?.title}
                        icon={currentFieldType.icon}
                    />
                )}
            </AccordionTrigger>
            <AccordionContent>
                <Form {...form}>
                    <form
                        onBlur={applyChanges}
                        onSubmit={applyChanges}
                        className='space-y-2'
                    >
                        <FormField
                            name='is_required'
                            control={form.control}
                            render={({ field }) => (
                                <FormItem className='flex items-center justify-between space-y-0 rounded-lg bg-white px-4 py-3'>
                                    <FormLabel>Required</FormLabel>
                                    <FormControl>
                                        <Switch
                                            checked={field.value}
                                            onCheckedChange={field.onChange}
                                        />
                                    </FormControl>
                                </FormItem>
                            )}
                        />
                        <FormField
                            name='label'
                            control={form.control}
                            render={({ field }) => (
                                <FormItem className='rounded-lg bg-white px-4 py-3'>
                                    <FormLabel>Label</FormLabel>
                                    <FormControl>
                                        <Input {...field} />
                                    </FormControl>
                                </FormItem>
                            )}
                        />
                        <div className='rounded-lg bg-white px-4 py-3'>
                            <FormField
                                control={form.control}
                                name='options'
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Options</FormLabel>
                                        <FormField
                                            name='multiple_answer_accepted'
                                            control={form.control}
                                            render={({ field }) => (
                                                <FormItem className='flex items-center space-x-2 space-y-0'>
                                                    <FormControl>
                                                        <Checkbox
                                                            className='border-metalic-blue data-[state=checked]:bg-metalic-blue'
                                                            checked={
                                                                field.value
                                                            }
                                                            onCheckedChange={
                                                                field.onChange
                                                            }
                                                        />
                                                    </FormControl>
                                                    <FormLabel>
                                                        Multiple answer accepted
                                                    </FormLabel>
                                                </FormItem>
                                            )}
                                        />
                                        <Accordion type='single' collapsible>
                                            <div className='flex flex-col gap-2'>
                                                {options.map(
                                                    (option, index) => (
                                                        <AccordionItem
                                                            key={index}
                                                            value={index.toString()}
                                                            className='rounded-lg border-0 bg-metalic-blue/5'
                                                        >
                                                            <AccordionTrigger className='px-4 py-2'>
                                                                {`Option ${
                                                                    index + 1
                                                                }`}
                                                            </AccordionTrigger>
                                                            <AccordionContent className='px-4 pb-0 pt-2'>
                                                                <div
                                                                    key={index}
                                                                >
                                                                    <Input
                                                                        value={
                                                                            option
                                                                        }
                                                                        onChange={(
                                                                            e,
                                                                        ) => {
                                                                            field.value[
                                                                                index
                                                                            ] =
                                                                                e.target.value;
                                                                            field.onChange(
                                                                                field.value,
                                                                            );
                                                                        }}
                                                                    />
                                                                    <div className='mt-2 flex justify-end'>
                                                                        <button
                                                                            type='button'
                                                                            className='text-red-500'
                                                                            onClick={() => {
                                                                                const newOptions =
                                                                                    [
                                                                                        ...field.value,
                                                                                    ];
                                                                                newOptions.splice(
                                                                                    index,
                                                                                    1,
                                                                                );
                                                                                field.onChange(
                                                                                    newOptions,
                                                                                );
                                                                            }}
                                                                        >
                                                                            <TrashIcon className='h-4 w-4' />
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </AccordionContent>
                                                        </AccordionItem>
                                                    ),
                                                )}
                                            </div>
                                        </Accordion>
                                        <Button
                                            variant='outline'
                                            className='gap-2 bg-metalic-blue px-4 text-white hover:bg-metalic-blue/90 hover:text-white'
                                            type='button'
                                            size='sm'
                                            onClick={() => {
                                                form.setValue(
                                                    'options',
                                                    field.value.concat(
                                                        'New option',
                                                    ),
                                                );
                                            }}
                                        >
                                            Add Option
                                        </Button>
                                    </FormItem>
                                )}
                            />
                        </div>
                    </form>
                </Form>
            </AccordionContent>
        </AccordionItem>
    );
}
export default ChecklistField;
