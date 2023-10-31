import z from 'zod';
import { useForm } from 'react-hook-form';
import { FileIcon } from 'lucide-react';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';

import useFormBuilder from '@/hooks/useFormBuilder';

function PageEditors() {
    const { pages } = useFormBuilder();

    return (
        <div className='h-full bg-[#f4f4f4] p-8'>
            <Accordion type='single' collapsible className='space-y-2'>
                {pages.map((page, i) => (
                    <AccordionItem value={page.page_id} className='border-0'>
                        <AccordionTrigger className='rounded-lg bg-white px-4'>
                            <div className='flex items-center gap-4 text-[12px]'>
                                <div className='grid h-[30px] w-[30px] place-items-center rounded-lg bg-metalic-blue/10 text-metalic-blue'>
                                    <FileIcon className='h-[19px] w-[19px]' />
                                </div>
                                Page {i + 1}
                            </div>
                        </AccordionTrigger>
                        <AccordionContent className='mt-2'>
                            <PageEditor
                                pageId={page.page_id}
                                pageTitle={page.page_title}
                            />
                        </AccordionContent>
                    </AccordionItem>
                ))}
            </Accordion>
        </div>
    );
}

type PageEditorProps = {
    pageId: string;
    pageTitle: string;
};

const schema = z.object({ page_title: z.string() });
type PageEditorForm = z.infer<typeof schema>;

function PageEditor({ pageId, pageTitle }: PageEditorProps) {
    const { pages, updatePage } = useFormBuilder();
    const page = pages.find((page) => page.page_id === pageId);

    const form = useForm<PageEditorForm>({
        defaultValues: { page_title: pageTitle },
    });

    const onSubmit = form.handleSubmit((values) => {
        if (!page) return;
        updatePage(pageId, { ...page, page_title: values.page_title });
    });

    return (
        <div className='rounded-lg bg-white p-4'>
            <Form {...form}>
                <form
                    onSubmit={(e) => e.preventDefault()}
                    onBlur={onSubmit}
                    className='space-y-8'
                >
                    <FormField
                        control={form.control}
                        name='page_title'
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel>Page Title</FormLabel>
                                <FormControl>
                                    <Input
                                        {...field}
                                        className='focus-visible:ring-transparent'
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                </form>
            </Form>
        </div>
    );
}

export default PageEditors;
