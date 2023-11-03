import { z } from 'zod';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
    ElementsType,
    FormElement,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from '@/components/ui/form';
import {
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Switch } from '@/components/ui/switch';
import useFormBuilder from '@/hooks/useFormBuilder';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import { DeleteIcon } from '@/assets/form-builder';
import useFieldTypes from '../../hooks/useFieldTypes';
import PropertyEditorHandle from '../PropertyEditorHandle';
import FieldTypeChanger from '../FieldTypeChanger';

const extraAttributes = {
    is_required: false,
    label: 'Please enter a number',
    type: 'number_input',
};

const schema = z.object({
    is_required: z.boolean(),
    label: z.string(),
    type: z.string(),
});

const NumberField: FormElement = {
    type: 'number',
    construct: (id) => ({
        id,
        type: 'number',
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
    const { fieldTypes, currentFieldType } = useFieldTypes(element.type);

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
                <button
                    onClick={() => removeField(current_page_id, element.id)}
                >
                    <DeleteIcon />
                </button>
            </div>
            <Separator className='my-2' />
            <div className='pointer-events-none space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                <Input type='number' readOnly />
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
                        icon={currentFieldType?.icon}
                        type={currentFieldType?.title}
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
                            name='type'
                            control={form.control}
                            render={({ field }) => {
                                return (
                                    <FormItem className='rounded-lg bg-white px-4 py-3'>
                                        <FormLabel>Type</FormLabel>
                                        <FormControl>
                                            <Select
                                                {...field}
                                                onValueChange={field.onChange}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value='number_input'>
                                                        Number Input
                                                    </SelectItem>
                                                    <SelectItem value='mobile_number_input'>
                                                        Mobile Number
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormControl>
                                    </FormItem>
                                );
                            }}
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
                    </form>
                </Form>
            </AccordionContent>
        </AccordionItem>
    );
}

export default NumberField;
