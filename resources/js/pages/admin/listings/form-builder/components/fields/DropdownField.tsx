import { z } from 'zod';
import { TrashIcon } from 'lucide-react';
import { FormElement, FormElementInstance } from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import {
    Select,
    SelectContent,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
    options: [],
};

const schema = z.object({
    is_required: z.boolean(),
    label: z.string(),
    options: z.array(z.string()).default([]),
});

const DropdownField: FormElement = {
    type: 'dropdown',
    construct: (id: string) => ({
        id,
        type: 'dropdown',
        extraAttributes,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField } = useFormBuilder();
    const elementInstance = element as FormElementInstance & {
        extraAttributes: typeof extraAttributes;
    };

    return (
        <div
            className='w-full rounded-lg border bg-white p-4 shadow-lg'
            onSelect={() => setSelectedField(element)}
        >
            <div className='flex justify-between'>
                <h1>{element.type}</h1>
                <TrashIcon onClick={() => removeField(element.id)} />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                <Select>
                    <SelectTrigger className='pointer-events-none'>
                        <SelectValue placeholder='Select a fruit' />
                    </SelectTrigger>
                    <SelectContent></SelectContent>
                </Select>
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

    const options = form.watch('options');

    const applyChanges = form.handleSubmit((values) => {
        updateField(element.id, {
            ...element,
            extraAttributes: { ...values },
        });
    });

    return (
        <AccordionItem value={element.id} className='border-0'>
            <AccordionTrigger className='mb-2 rounded-lg bg-white p-3 px-4'>
                {element.type}
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

export default DropdownField;
