import { z } from 'zod';
import { TrashIcon } from 'lucide-react';
import {
    ElementsType,
    FormElement,
    FormElementInstance,
    FormElements,
} from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Switch } from '@/components/ui/switch';

import useFormBuilder from '@/hooks/useFormBuilder';
import FieldTypeChanger from '../FieldTypeChanger';
import useFieldTypes from '../../useFieldTypes';
import PropertyEditorHandle from '../PropertyEditorHandle';

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
};

const schema = z.object({
    is_required: z.boolean(),
    label: z.string(),
});

const TimeField: FormElement = {
    type: 'time',
    construct: (id, page) => ({
        id,
        type: 'time',
        extraAttributes,
        page,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField, updateField, current_page } =
        useFormBuilder();
    const elementInstance = element as FormElementInstance & {
        extraAttributes: typeof extraAttributes;
    };

    const { currentFieldType, fieldTypes } = useFieldTypes(element.type);

    const handleValueChange = (value: ElementsType) => {
        updateField(
            element.id,
            FormElements[value].construct(element.id, current_page),
        );
    };

    return (
        <div
            className='w-full rounded-lg border bg-white p-4 shadow-lg'
            onSelect={() => setSelectedField(element)}
        >
            <div className='flex justify-between'>
                <FieldTypeChanger
                    icon={currentFieldType?.icon}
                    value={currentFieldType?.id}
                    onValueChange={handleValueChange}
                    data={fieldTypes}
                />
                <TrashIcon
                    className='text-red-500'
                    onClick={() => removeField(element.id)}
                />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                <Input type='time' readOnly />
            </div>
        </div>
    );
}

type PropertiesComponentProps = {
    element: FormElementInstance;
};
function PropertiesComponent({ element }: PropertiesComponentProps) {
    const { updateField } = useFormBuilder();
    const form = useForm<z.infer<typeof schema>>({
        defaultValues: element.extraAttributes,
        resolver: zodResolver(schema),
    });

    const { currentFieldType } = useFieldTypes(element.type);

    const applyChanges = form.handleSubmit((values) => {
        updateField(element.id, {
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
                    </form>
                </Form>
            </AccordionContent>
        </AccordionItem>
    );
}

export default TimeField;
