<style scoped>

    .avatar-design-1{
        background: linear-gradient(45deg, #9affa2, 25%, #70ff75, 30%, #0d8a11);
    }
    .avatar-design-2{
        background: linear-gradient(45deg, #ffa799, 25%, #ff9c8d, 30%, #c3432f);
    }
    .avatar-design-3{
        background: linear-gradient(45deg, #aff2ff, 25%, #8edaff, 30%, #00aaff);
    }
    .avatar-design-4{
        background: linear-gradient(45deg, #ffd7bd, 25%, #ffd2b4, 30%, #ff781f);
    }
    .avatar-design-5{
        background: linear-gradient(45deg, #ffc0df, 25%, #ffb5f9, 30%, #d20569);
    }
    .avatar-design-6{
        background: linear-gradient(45deg, #dfc7ff, 25%, #d3b1ff, 30%, #9b4fff);
    }
    .avatar-design-7{
        background: linear-gradient(45deg, #ffdaad, 25%, #ffc986, 30%, #b9721a);
    }
    .avatar-design-8{
        background: linear-gradient(45deg, #ae87b3, 25%, #ffbda4, 30%,#ae87b3);
    }
    .avatar-design-9{
        background: linear-gradient(45deg, #f3abff, 25%, #f098ff, 30%,#a536b7);
    }
    .avatar-design-10{
        background: linear-gradient(45deg, #be93c5, 25%, #ffff00, 30%,#be93c5);
    }
    .avatar-design-11{
        background: linear-gradient(45deg, #ffff55, 25%, #ffff00, 30%, #abab15);
    }
    .avatar-design-12{
        background: linear-gradient(45deg, #9cecfb, 25%, #65c7f7, 30%, #0052d4);
    }

</style>

<template>

    <Avatar :class="avatarDesign">
        {{ avatarInitials }}
    </Avatar>

</template>

<script>

    export default {
        props: {
            text:{
                type: String,
                default: 'A B'
            }
        },
        data(){
            return {
                numberOfDesigns: 12
            }
        },
        computed:{
            avatarInitials(){

                return this.text.charAt(0);

            },
            avatarDesign(){

                let characterCodes = this.text    // => Julian Tabona
                                    .split(' ')   // => ["J", "T"]
                                    .map(char => char.charAt(0).charCodeAt(0)) // => [65, 65];
                                    .join('');

                let designNumber = parseInt(characterCodes) % this.numberOfDesigns;
  
                return 'avatar-design-'+designNumber;

            }
        }
    };
</script>