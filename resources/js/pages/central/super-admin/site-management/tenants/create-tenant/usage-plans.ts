export const UsagePlansMap: Record<
    string,
    { description: string; inclusion: string[] }
> = {
    demo: {
        description:
            'A basic usage plan with limited resources, such as a small amount of storage and bandwidth.',
        inclusion: [
            '10,000 API calls per month',
            '500 MB of storage',
            '50 Mbps of bandwidth',
        ],
    },
    starter_plan: {
        description:
            'A basic usage plan with more resources than the free tier.',
        inclusion: [],
    },
    professional_plan: {
        description:
            'A usage plan with more resources than the starter plan, and access to advanced features.',
        inclusion: [],
    },
    enterprise_plan: {
        description:
            'A usage plan with the most resources of all the usage plans, including the most storage, bandwidth, and advanced features.',
        inclusion: [],
    },
    custom_plan: { description: '', inclusion: [] },
};
