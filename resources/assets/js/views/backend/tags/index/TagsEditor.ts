import swal from 'sweetalert';
import { Tag } from '../../../../api/tags/Tag';
import { TagsFacade } from '../../../../api/tags/TagsFacade';
import { Button, Form, Input, Modal } from '../../../../ui/components';
import { EventBus } from '../../../../utils/EventBus';

export class TagsEditor {

    private readonly bus: EventBus;
    private readonly modal: Modal;
    private readonly form: Form;
    private readonly closeButton: Button;
    private readonly submitButton: Button;

    private tag?: Tag;

    constructor(bus: EventBus, modal: JQuery) {
        this.bus = bus;

        this.modal = new Modal(modal);

        this.modal.onShown(() => {
            this.form.find('name').focus();
        });

        this.form = new Form({
            ajax: true,
            form: modal.find('form'),

            fields: {
                name: new Input(modal.find('[name="name"]')),
            },
        });

        this.form.on('submit', () => {
            // noinspection JSIgnoredPromiseFromCall
            this.submit();
        });

        this.closeButton = new Button(modal.find('.btn-close'));
        this.submitButton = new Button(modal.find('.btn-submit'));
    }

    public edit(tag: Tag): void {
        this.tag = tag;
        this.form.find('name').setValue(tag.name);
        this.modal.show();
    }

    private async submit(): Promise<void> {
        this.changeState('submitting');
        this.form.clearErrors();

        try {
            await TagsFacade.update(this.tag.id, this.form.serialize());

            this.bus.emit('tag::updated');
            this.modal.hide();

            await swal({
                title: 'Success',
                text: 'Tag has been updated.',
                icon: 'success',
            });
        } catch (error) {
            this.form.processErrors(error);
        }

        this.changeState('ready');
    }

    private changeState(state: string): void {
        switch (state) {
            case 'submitting':
                this.closeButton.disable();

                this.submitButton.disable();
                this.submitButton.showSpinner();

                break;

            case 'ready':
                this.closeButton.enable();

                this.submitButton.enable();
                this.submitButton.hideSpinner();

                break;
        }
    }

}